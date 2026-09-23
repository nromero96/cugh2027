<?php

namespace App\Services;

use App\Models\AbstractPost;
use App\Models\User;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AbstractReviewerAssignmentImportService
{
    private const MAX_ROWS = 10000;
    private const HEADERS = ['abstract_post_id', 'Revisor 1', 'Revisor 2', 'Revisor 3'];

    public function import(UploadedFile $file): array
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

        $rows = $sheet->rangeToArray('A1:Z'.max(1, $highestRow), null, true, true, false);
        $spreadsheet->disconnectWorksheets();
        list($headerIndex, $columns) = $this->findHeaders($rows);
        $records = [];
        $emails = [];
        foreach (array_slice($rows, $headerIndex + 1, null, true) as $index => $row) {
            $values = array_map(function ($key) use ($row, $columns) {
                return trim((string) ($row[$columns[$key]] ?? ''));
            }, array_keys(self::HEADERS));

            if (count(array_filter($values, function ($value) { return $value !== ''; })) === 0) {
                continue;
            }

            $records[] = ['row' => $index + 1, 'values' => $values];
            foreach (array_slice($values, 1) as $email) {
                if ($email !== '') {
                    $emails[Str::lower($email)] = true;
                }
            }
        }

        if (count($records) > self::MAX_ROWS) {
            throw new DomainException('The file exceeds the limit of '.number_format(self::MAX_ROWS).' data rows.');
        }

        $usersByEmail = [];
        foreach (array_chunk(array_keys($emails), 400) as $emailChunk) {
            $matches = User::query()->whereRaw('LOWER(email) IN ('.implode(',', array_fill(0, count($emailChunk), '?')).')', $emailChunk)
                ->get(['id', 'email']);
            foreach ($matches as $user) {
                $usersByEmail[Str::lower($user->email)][] = $user->id;
            }
        }

        $seenAbstracts = [];
        $rejected = [];
        $added = 0;
        $unchanged = 0;

        foreach ($records as $record) {
            $values = $record['values'];
            $idValue = $values[0];
            $reasons = [];
            $abstractId = null;

            if (!ctype_digit($idValue) || (int) $idValue < 1) {
                $reasons[] = 'Invalid or missing abstract_post_id.';
            } else {
                $abstractId = (int) $idValue;
                if (isset($seenAbstracts[$abstractId])) {
                    $reasons[] = 'Duplicate abstract_post_id in this file (first seen on row '.$seenAbstracts[$abstractId].').';
                } else {
                    $seenAbstracts[$abstractId] = $record['row'];
                }
            }

            $reviewerIds = [];
            $seenEmails = [];
            foreach (array_slice($values, 1) as $position => $email) {
                if ($email === '') {
                    continue;
                }
                $normalized = Str::lower($email);
                $column = 'Revisor '.($position + 1);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $reasons[] = $column.': invalid email.';
                } elseif (isset($seenEmails[$normalized])) {
                    $reasons[] = $column.': duplicate reviewer in this row.';
                } else {
                    $seenEmails[$normalized] = true;
                    $matches = $usersByEmail[$normalized] ?? [];
                    if (count($matches) === 0) {
                        $reasons[] = $column.': no registered user matches '.$email.'.';
                    } elseif (count($matches) > 1) {
                        $reasons[] = $column.': multiple registered users match '.$email.'.';
                    } else {
                        $reviewerIds[] = $matches[0];
                    }
                }
            }
            if (count($seenEmails) === 0) {
                $reasons[] = 'At least one reviewer email is required.';
            }

            if ($reasons) {
                $rejected[] = $this->rejectedRow($record, $reasons);
                continue;
            }

            $outcome = DB::transaction(function () use ($abstractId, $reviewerIds) {
                $abstract = AbstractPost::query()->whereKey($abstractId)->lockForUpdate()->first();
                if (!$abstract) {
                    return 'Abstract not found.';
                }

                $existing = DB::table('abstract_post_reviewers')
                    ->where('abstract_post_id', $abstractId)
                    ->lockForUpdate()
                    ->get(['reviewer_id', 'score_1', 'score_2', 'score_3', 'score_4', 'score_5', 'average_score']);
                $existingIds = $existing->pluck('reviewer_id')->map(function ($id) { return (int) $id; })->all();
                $missingIds = array_values(array_diff($reviewerIds, $existingIds));

                if (!$missingIds) {
                    return 0;
                }
                if ($abstract->status === 'qualified' || $existing->contains(function ($assignment) {
                    return $assignment->average_score !== null
                        || $assignment->score_1 !== null || $assignment->score_2 !== null
                        || $assignment->score_3 !== null || $assignment->score_4 !== null
                        || $assignment->score_5 !== null;
                })) {
                    return 'Assignments cannot be changed after an evaluation has been submitted.';
                }
                if (count($existingIds) + count($missingIds) > 3) {
                    return 'Adding these reviewers would exceed the maximum of 3 assignments.';
                }

                $abstract->reviewers()->syncWithoutDetaching($missingIds);
                return count($missingIds);
            });

            if (is_string($outcome)) {
                $rejected[] = $this->rejectedRow($record, [$outcome]);
            } elseif ($outcome === 0) {
                $unchanged++;
            } else {
                $added += $outcome;
            }
        }

        return [
            'rows' => count($records),
            'added' => $added,
            'unchanged' => $unchanged,
            'rejected_rows' => $rejected,
        ];
    }

    public function rejectedRowsCsv(array $rows): string
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, array_merge(['Excel Row'], self::HEADERS, ['Import Error']));
        foreach ($rows as $row) {
            $cells = array_merge([$row['row']], $row['values'], [$row['error']]);
            fputcsv($stream, array_map(function ($value) {
                $text = (string) $value;
                return preg_match('/^[\s]*[=+\-@]/u', $text) ? "'".$text : $text;
            }, $cells));
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return $csv;
    }

    private function findHeaders(array $rows): array
    {
        $expected = ['abstractpostid' => 0, 'revisor1' => 1, 'revisor2' => 2, 'revisor3' => 3,
            'reviewer1' => 1, 'reviewer2' => 2, 'reviewer3' => 3];
        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $row) {
            $columns = [];
            foreach ($row as $columnIndex => $header) {
                $normalized = preg_replace('/[^a-z0-9]/', '', Str::lower(trim((string) $header)));
                if (isset($expected[$normalized])) {
                    $columns[$expected[$normalized]] = $columnIndex;
                }
            }
            if (count($columns) === 4) {
                return [$rowIndex, $columns];
            }
        }
        throw new DomainException('The file must contain abstract_post_id and Revisor 1, Revisor 2, Revisor 3 columns.');
    }

    private function rejectedRow(array $record, array $reasons): array
    {
        return ['row' => $record['row'], 'values' => $record['values'], 'error' => implode(' ', $reasons)];
    }
}
