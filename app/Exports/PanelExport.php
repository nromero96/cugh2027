<?php

namespace App\Exports;

use App\Models\Panel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PanelExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Panel::orderBy('id')->get();
    }

    public function headings(): array
    {
        $headings = [
            'ID', 'Language', 'Sub-Themes', 'Other Sub-Theme', 'Title',
            'Contact Salutation', 'Contact Name', 'Contact Institution',
            'Contact Country', 'Contact Phone', 'Contact E-mail',
            'Moderator Name', 'Moderator Position', 'Moderator Institution',
            'Moderator Country',
        ];

        return array_merge($headings, [
            'Speakers',
            'Panel Description',
            'Learning Objectives',
            'Status',
            'Created At',
            'Updated At',
        ]);
    }

    public function map($panel): array
    {
        $speakers = is_array($panel->speakers) ? $panel->speakers : [];
        $speakersText = collect($speakers)
            ->values()
            ->map(function ($speaker, $index) {
                $details = collect([
                    'Name' => $speaker['name'] ?? null,
                    'Position' => $speaker['position'] ?? null,
                    'Institution' => $speaker['institution'] ?? null,
                    'Country' => $speaker['country'] ?? null,
                ])->filter(function ($value) {
                    return $value !== null && $value !== '';
                })->map(function ($value, $label) {
                    return "{$label}: {$value}";
                })->implode('; ');

                return $details !== '' ? 'Speaker ' . ($index + 1) . " ({$details})" : null;
            })
            ->filter()
            ->implode("\n");

        return [
            $panel->id,
            $panel->language,
            collect($panel->subthemes ?? [])->implode(' | '),
            $panel->subthemes_other,
            $panel->title,
            $panel->contact_salutation,
            $panel->contact_name,
            $panel->contact_institution,
            $panel->contact_country,
            $panel->contact_phone,
            $panel->contact_email,
            $panel->moderator_name,
            $panel->moderator_position,
            $panel->moderator_institution,
            $panel->moderator_country,
            $speakersText,
            $panel->description,
            $panel->learning_objectives,
            $panel->status,
            optional($panel->created_at)->format('Y-m-d H:i:s'),
            optional($panel->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:U1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'],
            ],
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:U1');
        $sheet->getStyle('A:U')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('C:R')->getAlignment()->setWrapText(true);

        for ($columnIndex = 1; $columnIndex <= 21; $columnIndex++) {
            $column = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($column)->setWidth(22);
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('P')->setWidth(65);
        $sheet->getColumnDimension('Q')->setWidth(60);
        $sheet->getColumnDimension('R')->setWidth(60);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);

        return [];
    }
}
