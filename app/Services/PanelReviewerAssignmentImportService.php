<?php

namespace App\Services;

use App\Models\Panel;
use App\Models\User;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PanelReviewerAssignmentImportService
{
    private const MAX_ROWS = 10000;
    private const HEADERS = ['panel_id', 'Revisor 1', 'Revisor 2'];

    public function import(UploadedFile $file): array
    {
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $highestRow = (int) $spreadsheet->getActiveSheet()->getHighestDataRow();
        if ($highestRow > self::MAX_ROWS + 10) {
            $spreadsheet->disconnectWorksheets();
            throw new DomainException('The file exceeds the limit of '.number_format(self::MAX_ROWS).' data rows.');
        }
        $rows = $spreadsheet->getActiveSheet()->rangeToArray('A1:Z'.max(1, $highestRow), null, true, true, false);
        $spreadsheet->disconnectWorksheets();
        list($headerIndex, $columns) = $this->findHeaders($rows);

        $records = [];
        $emails = [];
        foreach (array_slice($rows, $headerIndex + 1, null, true) as $index => $row) {
            $values = array_map(function ($key) use ($row, $columns) {
                return trim((string) ($row[$columns[$key]] ?? ''));
            }, array_keys(self::HEADERS));
            if (!count(array_filter($values, function ($value) { return $value !== ''; }))) {
                continue;
            }
            $records[] = ['row' => $index + 1, 'values' => $values];
            foreach (array_slice($values, 1) as $email) {
                if ($email !== '') $emails[Str::lower($email)] = true;
            }
        }
        if (count($records) > self::MAX_ROWS) {
            throw new DomainException('The file exceeds the limit of '.number_format(self::MAX_ROWS).' data rows.');
        }

        $usersByEmail = [];
        foreach (array_chunk(array_keys($emails), 400) as $chunk) {
            $matches = User::whereRaw('LOWER(email) IN ('.implode(',', array_fill(0, count($chunk), '?')).')', $chunk)
                ->get(['id', 'email']);
            foreach ($matches as $user) {
                $usersByEmail[Str::lower($user->email)][] = $user->id;
            }
        }

        $seenPanels = [];
        $rejected = [];
        $added = 0;
        $unchanged = 0;
        foreach ($records as $record) {
            $values = $record['values'];
            $reasons = [];
            $panelId = null;
            if (!ctype_digit($values[0]) || (int) $values[0] < 1) {
                $reasons[] = 'Invalid or missing panel_id.';
            } else {
                $panelId = (int) $values[0];
                if (isset($seenPanels[$panelId])) {
                    $reasons[] = 'Duplicate panel_id in this file (first seen on row '.$seenPanels[$panelId].').';
                } else {
                    $seenPanels[$panelId] = $record['row'];
                }
            }

            $reviewerIds = [];
            $seenEmails = [];
            foreach (array_slice($values, 1) as $position => $email) {
                if ($email === '') continue;
                $normalized = Str::lower($email);
                $column = 'Revisor '.($position + 1);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $reasons[] = $column.': invalid email.';
                } elseif (isset($seenEmails[$normalized])) {
                    $reasons[] = $column.': duplicate reviewer in this row.';
                } else {
                    $seenEmails[$normalized] = true;
                    $matches = $usersByEmail[$normalized] ?? [];
                    if (!$matches) {
                        $reasons[] = $column.': no registered user matches '.$email.'.';
                    } elseif (count($matches) > 1) {
                        $reasons[] = $column.': multiple registered users match '.$email.'.';
                    } else {
                        $reviewerIds[] = $matches[0];
                    }
                }
            }
            if (!$seenEmails) $reasons[] = 'At least one reviewer email is required.';
            if ($reasons) {
                $rejected[] = $this->rejectedRow($record, $reasons);
                continue;
            }

            $outcome = DB::transaction(function () use ($panelId, $reviewerIds) {
                $panel = Panel::whereKey($panelId)->lockForUpdate()->first();
                if (!$panel) return 'Panel not found.';

                $existing = DB::table('panel_reviewers')->where('panel_id', $panelId)->lockForUpdate()->get();
                $existingIds = $existing->pluck('reviewer_id')->map(function ($id) { return (int) $id; })->all();
                $missingIds = array_values(array_diff($reviewerIds, $existingIds));
                if (!$missingIds) return 0;

                if (in_array($panel->status, ['Qualified', 'Rejected'], true) || $existing->contains(function ($assignment) {
                    if ($assignment->average_score !== null) return true;
                    foreach (range(1, 8) as $number) {
                        if ($assignment->{'score_'.$number} !== null) return true;
                    }
                    return false;
                })) {
                    return 'Assignments cannot be changed after an evaluation has been submitted or a panel is closed.';
                }
                if (count($existingIds) + count($missingIds) > 2) {
                    return 'Adding these reviewers would exceed the maximum of 2 assignments.';
                }

                $panel->reviewers()->syncWithoutDetaching($missingIds);
                return count($missingIds);
            });

            if (is_string($outcome)) $rejected[] = $this->rejectedRow($record, [$outcome]);
            elseif ($outcome === 0) $unchanged++;
            else $added += $outcome;
        }

        return ['rows' => count($records), 'added' => $added, 'unchanged' => $unchanged, 'rejected_rows' => $rejected];
    }

    public function rejectedRowsCsv(array $rows): string
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, array_merge(['Excel Row'], self::HEADERS, ['Import Error']));
        foreach ($rows as $row) {
            fputcsv($stream, array_map(function ($value) {
                $text = (string) $value;
                return preg_match('/^[\s]*[=+\-@]/u', $text) ? "'".$text : $text;
            }, array_merge([$row['row']], $row['values'], [$row['error']])));
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return $csv;
    }

    private function findHeaders(array $rows): array
    {
        $expected = ['panelid' => 0, 'revisor1' => 1, 'reviewer1' => 1, 'revisor2' => 2, 'reviewer2' => 2];
        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $row) {
            $columns = [];
            foreach ($row as $columnIndex => $header) {
                $normalized = preg_replace('/[^a-z0-9]/', '', Str::lower(trim((string) $header)));
                if (isset($expected[$normalized])) $columns[$expected[$normalized]] = $columnIndex;
            }
            if (count($columns) === 3) return [$rowIndex, $columns];
        }
        throw new DomainException('The file must contain panel_id, Revisor 1 and Revisor 2 columns.');
    }

    private function rejectedRow(array $record, array $reasons): array
    {
        return ['row' => $record['row'], 'values' => $record['values'], 'error' => implode(' ', $reasons)];
    }
}
