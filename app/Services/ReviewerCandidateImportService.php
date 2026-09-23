<?php

namespace App\Services;

use App\Models\ReviewerCandidate;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReviewerCandidateImportService
{
    public const HEADERS = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'salutation' => 'Salutation',
        'professional_position_academic_title' => 'Professional Position / Academic Title',
        'institution' => 'Institution',
        'country' => 'Country',
        'email' => 'Email',
        'review_interest' => 'Are you interested in reviewing abstracts and or panel proposals?',
        'panel_languages' => 'Can you judge panels in:',
        'subthemes' => 'All abstracts and panels will be categorized under these 6 subthemes. Please select all subthemes you feel comfortable judging.',
    ];

    private const MAX_ROWS = 10000;

    public function import(UploadedFile $file)
    {
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestDataRow();

        if ($highestRow > self::MAX_ROWS + 10) {
            $spreadsheet->disconnectWorksheets();
            throw new DomainException('The file exceeds the limit of '.number_format(self::MAX_ROWS).' data rows.');
        }

        // Read a few extra columns so the import still works if the source file
        // includes an auxiliary column or changes the order of the 10 fields.
        $rows = $sheet->rangeToArray('A1:Z'.$highestRow, null, true, true, false);
        $spreadsheet->disconnectWorksheets();

        list($headerRowIndex, $columnMap) = $this->findHeaderRow($rows);
        $records = [];
        $sourceRows = [];
        $rejectedRows = [];
        $errors = [];
        $sourceName = Str::limit(basename($file->getClientOriginalName()), 255, '');

        foreach (array_slice($rows, $headerRowIndex + 1, null, true) as $zeroBasedRow => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $excelRow = $zeroBasedRow + 1;
            $sourceRow = $this->mappedSourceRow($row, $columnMap);
            $email = Str::lower($sourceRow['email']);

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reason = 'Row '.$excelRow.': invalid or missing email.';
                $errors[] = $reason;
                $sourceRow['import_error'] = $reason;
                $rejectedRows[] = $sourceRow;
                continue;
            }

            if (isset($records[$email])) {
                $reason = 'Row '.$sourceRows[$email]['excel_row'].': duplicate email in the import file; row '.$excelRow.' was used instead.';
                $errors[] = $reason;
                $rejectedRow = $sourceRows[$email]['data'];
                $rejectedRow['import_error'] = $reason;
                $rejectedRows[] = $rejectedRow;
            }

            $record = [];
            foreach (array_keys(self::HEADERS) as $field) {
                $record[$field] = $sourceRow[$field] === '' ? null : $sourceRow[$field];
            }

            $record['email'] = $email;
            $record['source_file'] = $sourceName;
            $record['source_row'] = $excelRow;
            $record['imported_at'] = now();
            $record['created_at'] = now();
            $record['updated_at'] = now();
            $records[$email] = $record;
            $sourceRows[$email] = ['excel_row' => $excelRow, 'data' => $sourceRow];
        }

        if (empty($records)) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => count($rejectedRows),
                'errors' => array_slice($errors, 0, 20),
                'rejected_rows' => $rejectedRows,
            ];
        }

        $existing = collect();
        foreach (array_chunk(array_keys($records), 500) as $emails) {
            $existing = $existing->merge(ReviewerCandidate::whereIn('email', $emails)->pluck('email'));
        }
        $existing = $existing->map(function ($email) {
            return Str::lower($email);
        })->unique();

        $updateColumns = array_values(array_diff(array_keys(reset($records)), ['email', 'created_at']));

        DB::transaction(function () use ($records, $updateColumns) {
            foreach (array_chunk(array_values($records), 500) as $chunk) {
                ReviewerCandidate::upsert($chunk, ['email'], $updateColumns);
            }
        });

        return [
            'created' => count($records) - $existing->count(),
            'updated' => $existing->count(),
            'skipped' => count($rejectedRows),
            'errors' => array_slice($errors, 0, 20),
            'rejected_rows' => $rejectedRows,
        ];
    }

    public function rejectedRowsCsv(array $rows)
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, array_merge(array_values(self::HEADERS), ['Import Error']));

        foreach ($rows as $row) {
            $values = [];
            foreach (array_keys(self::HEADERS) as $field) {
                $values[] = $row[$field] ?? '';
            }
            $values[] = $row['import_error'] ?? 'The row was not imported.';
            fputcsv($stream, $values);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv;
    }

    public function normalizedHeader($value)
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);
        $value = str_replace("\xC2\xA0", ' ', $value);
        $value = Str::lower(trim($value));

        return trim((string) preg_replace('/[^\pL\pN]+/u', ' ', $value));
    }

    private function findHeaderRow(array $rows)
    {
        $expected = [];
        foreach (self::HEADERS as $field => $label) {
            $expected[$this->normalizedHeader($label)] = $field;
        }

        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $row) {
            $map = [];
            foreach ($row as $columnIndex => $heading) {
                $normalized = $this->normalizedHeader($heading);
                if (isset($expected[$normalized])) {
                    $map[$expected[$normalized]] = $columnIndex;
                }
            }

            if (count($map) === count(self::HEADERS)) {
                return [$rowIndex, $map];
            }
        }

        $expectedLabels = implode(', ', array_values(self::HEADERS));
        throw new DomainException('The spreadsheet headers do not match the template. Expected: '.$expectedLabels.'.');
    }

    private function isEmptyRow(array $row)
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function cell(array $row, $index)
    {
        return trim((string) ($row[$index] ?? ''));
    }

    private function mappedSourceRow(array $row, array $columnMap)
    {
        $mapped = [];
        foreach (array_keys(self::HEADERS) as $field) {
            $mapped[$field] = $this->cell($row, $columnMap[$field]);
        }

        return $mapped;
    }

}
