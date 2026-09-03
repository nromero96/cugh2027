<?php

namespace App\Exports;

use App\Models\Workshop;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkshopExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomValueBinder
{
    public function query()
    {
        return Workshop::query()->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID', 'Lead Name', 'Lead Institution', 'Lead Title', 'Lead E-mail',
            'Lead Phone', 'Lead Cell Phone', 'Workshop Title', 'Workshop Description',
            'Learning Objectives', 'Speakers', 'Time Slot', 'Day Length', 'Room Setup',
            'Attendees', 'Notes', 'Payment Lead Same as Workshop Lead', 'Payment Name',
            'Payment Institution', 'Payment Title', 'Payment E-mail', 'Payment Phone',
            'Payment Cell Phone', 'Signature File', 'Place and Date', 'Created At', 'Updated At',
        ];
    }

    public function map($workshop): array
    {
        $sameLead = (bool) $workshop->payment_lead_same;

        return [
            $workshop->id,
            $workshop->lead_name,
            $workshop->lead_institution,
            $workshop->lead_title,
            $workshop->lead_email,
            $workshop->lead_phone,
            $workshop->lead_cell,
            $workshop->workshop_title,
            $workshop->workshop_desc,
            $workshop->workshop_objectives,
            $workshop->workshop_speakers,
            $workshop->time_slot,
            $workshop->day_length,
            $workshop->room_setup,
            $workshop->attendees,
            $workshop->notes,
            $sameLead ? 'Yes' : 'No',
            $sameLead ? $workshop->lead_name : $workshop->payment_name,
            $sameLead ? $workshop->lead_institution : $workshop->payment_institution,
            $sameLead ? $workshop->lead_title : $workshop->payment_title,
            $sameLead ? $workshop->lead_email : $workshop->payment_email,
            $sameLead ? $workshop->lead_phone : $workshop->payment_phone,
            $sameLead ? $workshop->lead_cell : $workshop->payment_cell,
            $workshop->signature_path,
            $workshop->place_date,
            optional($workshop->created_at)->format('Y-m-d H:i:s'),
            optional($workshop->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
        $sheet->getStyle('A:AA')->getAlignment()->setVertical('top')->setWrapText(true);

        for ($index = 1; $index <= count($this->headings()); $index++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setWidth(24);
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        foreach (['H', 'I', 'J', 'K', 'P'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(50);
        }

        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF198754']]]];
    }
}
