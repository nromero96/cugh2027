<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use App\Models\AbstractPost;
use App\Models\User;

class AbstractPostExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AbstractPost::join(
                'users',
                'users.id',
                '=',
                'abstract_posts.user_id'
            )
            ->leftJoin(
                'countries',
                'countries.id',
                '=',
                'abstract_posts.main_author_country_id'
            )
            ->select(
                'abstract_posts.id',

                // Usuario que registró el abstract
                'users.email',

                // Abstract
                'abstract_posts.main_author',
                'abstract_posts.presentation_type',
                'abstract_posts.title',
                'abstract_posts.co_authors',
                'abstract_posts.institutions',
                'abstract_posts.abstract_type',
                'abstract_posts.subtopic',
                'abstract_posts.body',
                'abstract_posts.keywords',
                'abstract_posts.status',
                'abstract_posts.created_at',
                'abstract_posts.updated_at',

                'countries.name as country_name'
            )
            ->with('reviewers:id,email')
            ->get();
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Main author/presenter name',
            'E-mail',
            'Presentation Type',
            'Title',
            'Co-authors',
            'Institutions',
            'Abstract Type',
            'Sub theme',
            'Body',
            'Keywords',
            'Status',
            'Created At',
            'Updated At',
            'Country',
        ];

        foreach (range(1, 3) as $reviewerNumber) {
            $headings[] = 'Reviewer '.$reviewerNumber.' Email';
            foreach (range(1, 5) as $scoreNumber) {
                $headings[] = 'Reviewer '.$reviewerNumber.' Score '.$scoreNumber;
            }
            $headings[] = 'Reviewer '.$reviewerNumber.' Average';
        }
        $headings[] = 'Final Average';

        return $headings;
    }

    public function map($abstractPost): array
    {
        /*
        |--------------------------------------------------------------------------
        | Main author
        |--------------------------------------------------------------------------
        */

        $mainAuthorArray = $this->decodeJsonField(
            $abstractPost->main_author
        );

        $mainAuthorName = trim(
            ($mainAuthorArray['name'] ?? '') . ' ' .
            ($mainAuthorArray['lastname'] ?? '')
        );

        /*
        |--------------------------------------------------------------------------
        | Co-authors
        |--------------------------------------------------------------------------
        */

        $coAuthorsArray = $this->decodeJsonField(
            $abstractPost->co_authors
        );

        $coAuthorsList = collect($coAuthorsArray)->keyBy('id');

        $coAuthors = collect($coAuthorsArray)
            ->map(function ($author) {
                return trim(
                    ($author['name'] ?? '') . ' ' .
                    ($author['lastname'] ?? '')
                );
            })
            ->filter()
            ->implode(', ');

        /*
        |--------------------------------------------------------------------------
        | Institutions
        |--------------------------------------------------------------------------
        */

        $institutionsArray = $this->decodeJsonField(
            $abstractPost->institutions
        );

        $institutions = collect($institutionsArray)
            ->map(function ($institution) use (
                $coAuthorsList,
                $mainAuthorName
            ) {
                $authors = collect(
                    $institution['coauthors'] ?? []
                )
                    ->map(function ($authorId) use (
                        $coAuthorsList,
                        $mainAuthorName
                    ) {
                        // Autor principal
                        if ($authorId === 'main_author') {
                            return $mainAuthorName;
                        }

                        // Coautor
                        $author = $coAuthorsList->get($authorId);

                        if (!$author) {
                            return null;
                        }

                        return trim(
                            ($author['name'] ?? '') . ' ' .
                            ($author['lastname'] ?? '')
                        );
                    })
                    ->filter()
                    ->implode(', ');

                $institutionName = trim(
                    $institution['name'] ?? ''
                );

                if (!$institutionName) {
                    return null;
                }

                return $authors
                    ? $institutionName . ' (' . $authors . ')'
                    : $institutionName;
            })
            ->filter()
            ->implode(' | ');

        /*
        |--------------------------------------------------------------------------
        | Keywords
        |--------------------------------------------------------------------------
        */

        $keywordsArray = $this->decodeJsonField(
            $abstractPost->keywords
        );

        $keywords = collect($keywordsArray)
            ->filter()
            ->implode(', ');

        $row = [
            $abstractPost->id,
            $mainAuthorName,
            $abstractPost->email,
            $abstractPost->presentation_type,
            $abstractPost->title,
            $coAuthors,
            $institutions,
            $abstractPost->abstract_type,
            $abstractPost->subtopic,
            $abstractPost->body,
            $keywords,
            $abstractPost->status,
            $abstractPost->created_at,
            $abstractPost->updated_at,
            $abstractPost->country_name,
        ];

        $reviewers = $abstractPost->reviewers->sortBy('id')->values();
        $submittedScores = 0;
        $submittedCount = 0;
        foreach (range(0, 2) as $slot) {
            $reviewer = $reviewers->get($slot);
            $submitted = $reviewer && $reviewer->pivot->average_score !== null;
            $row[] = $reviewer ? $reviewer->email : null;

            foreach (range(1, 5) as $scoreNumber) {
                $score = $submitted ? $reviewer->pivot->{'score_'.$scoreNumber} : null;
                $row[] = $score === null ? null : (int) $score;
                if ($score !== null) {
                    $submittedScores += (int) $score;
                }
            }
            $row[] = $submitted ? (float) $reviewer->pivot->average_score : null;
            if ($submitted) {
                $submittedCount++;
            }
        }

        $row[] = $reviewers->isNotEmpty() && $submittedCount === $reviewers->count()
            ? intdiv($submittedScores * 100, $submittedCount * 5) / 100
            : null;

        return $row;
    }

    /**
     * Convierte un campo JSON en array.
     * También permite compatibilidad con registros antiguos
     * que hayan sido codificados dos veces.
     */
    private function decodeJsonField($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!$value || !is_string($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        // Compatibilidad con JSON doblemente codificado
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function styles(Worksheet $sheet) {
        $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'c00000',
                ],
            ],
        ],);
        // Make each reviewer's seven-column block easy to distinguish in Excel.
        $reviewerColors = [
            ['start' => 'P', 'end' => 'V', 'header' => 'FF28539A', 'body' => 'FFEDF3FC'],
            ['start' => 'W', 'end' => 'AC', 'header' => 'FF087F8C', 'body' => 'FFEAF7F7'],
            ['start' => 'AD', 'end' => 'AJ', 'header' => 'FFA05A00', 'body' => 'FFFFF4E3'],
        ];
        foreach ($reviewerColors as $colors) {
            $sheet->getStyle($colors['start'].'1:'.$colors['end'].'1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => $colors['header']],
                ],
            ]);
            if ($lastRow >= 2) {
                $sheet->getStyle($colors['start'].'2:'.$colors['end'].$lastRow)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($colors['body']);
            }
        }
        $sheet->getStyle('AK1:AK'.$lastRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCC1F2F'],
            ],
        ]);
        //aplicar anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(29);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(104);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(27);
        $sheet->getColumnDimension('I')->setWidth(50);
        $sheet->getColumnDimension('J')->setWidth(65);
        $sheet->getColumnDimension('K')->setWidth(40);
        $sheet->getColumnDimension('L')->setWidth(13);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(13);

        foreach (range(1, 3) as $reviewerNumber) {
            $emailColumnIndex = 16 + ($reviewerNumber - 1) * 7;
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($emailColumnIndex))->setWidth(29);
            foreach (range(1, 5) as $scoreNumber) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($emailColumnIndex + $scoreNumber))->setWidth(17);
            }
            $averageColumn = Coordinate::stringFromColumnIndex($emailColumnIndex + 6);
            $sheet->getColumnDimension($averageColumn)->setWidth(19);
            $sheet->getStyle($averageColumn.'2:'.$averageColumn.$sheet->getHighestRow())
                ->getNumberFormat()->setFormatCode('0.00');
        }
        $sheet->getColumnDimension($lastColumn)->setWidth(18);
        $sheet->getStyle($lastColumn.'2:'.$lastColumn.$sheet->getHighestRow())
            ->getNumberFormat()->setFormatCode('0.00');

        // Permitir saltos de línea en el abstract
        $sheet->getStyle('J:J')->getAlignment()->setWrapText(true);

        // Alinear el contenido arriba
        $sheet->getStyle('A:'.$lastColumn)->getAlignment()->setVertical(
            Alignment::VERTICAL_TOP
        );

        // Altura fija para filas de datos
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $sheet->getRowDimension($row)->setRowHeight(90);
        }
    }

}
