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

use App\Models\AbstractPost;
use App\Models\User;

class AbstractPostExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AbstractPost::join('users', 'users.id', '=', 'abstract_posts.user_id')
                            ->leftJoin('countries', 'countries.id', '=', 'users.country')
                            ->select('abstract_posts.id', 
                                    'users.name',
                                    'users.lastname',
                                    'users.second_lastname',
                                    'users.email',
                                    //AbstractPost
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
                                    'countries.name as country_name')
                            ->get();

    }

    public function headings(): array
    {
        return [
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
    }

    public function map($abstractPost): array
    {

        // Co-authors
        $coAuthorsArray = json_decode($abstractPost->co_authors, true) ?? [];

        $coAuthorsList = collect($coAuthorsArray)->keyBy('id');

        $coAuthors = collect($coAuthorsArray)
            ->map(function ($author) {
                return trim(($author['name'] ?? '') . ' ' . ($author['lastname'] ?? ''));
            })
            ->implode(', ');

        $institutionsArray = json_decode($abstractPost->institutions, true) ?? [];

        $institutions = collect($institutionsArray)
            ->map(function ($institution) use ($coAuthorsList) {
                $authors = collect($institution['coauthors'] ?? [])
                    ->map(function ($authorId) use ($coAuthorsList) {
                        $author = $coAuthorsList->get($authorId);

                        return $author
                            ? trim(($author['name'] ?? '') . ' ' . ($author['lastname'] ?? ''))
                            : '';
                    })
                    ->filter()
                    ->implode(', ');

                return $authors
                    ? ($institution['name'] ?? '') . ' (' . $authors . ')'
                    : ($institution['name'] ?? '');
            })
            ->filter()
            ->implode(' | ');

        $keywords = collect(json_decode($abstractPost->keywords, true) ?? [])
        ->implode(', ');

        return [
            $abstractPost->id,
            $abstractPost->name.' '.$abstractPost->lastname.' '.$abstractPost->second_lastname,
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
            $abstractPost->country_name
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A1:O1')->applyFromArray([
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
        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(13);

        // Permitir saltos de línea en el abstract
        $sheet->getStyle('J:J')->getAlignment()->setWrapText(true);

        // Alinear el contenido arriba
        $sheet->getStyle('A:O')->getAlignment()->setVertical(
            Alignment::VERTICAL_TOP
        );

        // Altura fija para filas de datos
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $sheet->getRowDimension($row)->setRowHeight(90);
        }
    }

}
