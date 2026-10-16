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
    private $rejectedOnly;

    public function __construct(bool $rejectedOnly = false)
    {
        $this->rejectedOnly = $rejectedOnly;
    }

    public function collection()
    {
        return Panel::query()
            ->when($this->rejectedOnly, function ($query) {
                $query->where('status', 'Rejected');
            }, function ($query) {
                $query->where('status', '!=', 'Rejected');
            })
            ->orderBy('id')
            ->with('reviewers:id,email')
            ->get();
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

        $headings = array_merge($headings, [
            'Speakers',
            'Panel Description',
            'Learning Objectives',
            'Status',
            'Created At',
            'Updated At',
        ]);

        foreach (range(1, 2) as $reviewerNumber) {
            $headings[] = 'Reviewer '.$reviewerNumber.' Email';
            foreach (range(1, 8) as $scoreNumber) {
                $headings[] = 'Reviewer '.$reviewerNumber.' Score '.$scoreNumber;
            }
            $headings[] = 'Reviewer '.$reviewerNumber.' Average';
        }
        $headings[] = 'Final Average';

        return $headings;
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

        $row = [
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

        $reviewers = $panel->reviewers->sortBy('id')->values();
        $submittedScores = 0;
        $submittedCount = 0;
        foreach (range(0, 1) as $slot) {
            $reviewer = $reviewers->get($slot);
            $submitted = $reviewer && $reviewer->pivot->average_score !== null;
            $row[] = $reviewer ? $reviewer->email : null;

            foreach (range(1, 8) as $scoreNumber) {
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
            ? $submittedScores / ($submittedCount * 8)
            : null;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));
        $lastRow = $sheet->getHighestRow();
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

        foreach ([
            ['start' => 'V', 'end' => 'AE', 'header' => 'FF28539A', 'body' => 'FFEDF3FC'],
            ['start' => 'AF', 'end' => 'AO', 'header' => 'FF087F8C', 'body' => 'FFEAF7F7'],
        ] as $colors) {
            $sheet->getStyle($colors['start'].'1:'.$colors['end'].'1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colors['header']]],
            ]);
            if ($lastRow >= 2) {
                $sheet->getStyle($colors['start'].'2:'.$colors['end'].$lastRow)
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($colors['body']);
            }
        }
        $sheet->getStyle('AP1:AP'.$lastRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCC1F2F']],
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:'.$lastColumn.'1');
        $sheet->getStyle('A:'.$lastColumn)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('C:R')->getAlignment()->setWrapText(true);
        $sheet->getStyle('V1:'.$lastColumn.'1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(32);

        for ($columnIndex = 1; $columnIndex <= count($this->headings()); $columnIndex++) {
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
        foreach (range(0, 1) as $slot) {
            $emailColumnIndex = 22 + $slot * 10;
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($emailColumnIndex))->setWidth(30);
            foreach (range(1, 8) as $scoreNumber) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($emailColumnIndex + $scoreNumber))->setWidth(19);
            }
            $averageColumn = Coordinate::stringFromColumnIndex($emailColumnIndex + 9);
            $sheet->getColumnDimension($averageColumn)->setWidth(20);
            if ($lastRow >= 2) {
                $sheet->getStyle($averageColumn.'2:'.$averageColumn.$lastRow)
                    ->getNumberFormat()->setFormatCode('0.00');
            }
        }
        $sheet->getColumnDimension('AP')->setWidth(20);
        if ($lastRow >= 2) {
            $sheet->getStyle('AP2:AP'.$lastRow)->getNumberFormat()->setFormatCode('0.00');
        }

        return [];
    }
}
