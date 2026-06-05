<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Inscription;

class ExporInscriptions implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //join users, payments
        return Inscription::join('users', 'users.id', '=', 'inscriptions.user_id')
                            ->select('inscriptions.id', 
                                    'users.salutation',
                                    'users.name',
                                    'users.lastname',
                                    'users.second_lastname',
                                    'users.degrees',
                                    'users.is_cugh_member',
                                    'users.document_type',
                                    'users.document_number',
                                    'nationalities.name as user_nationality',
                                    'users.gender',
                                    'users.occupation',

                                    'users.workplace',
                                    'users.address',
                                    'users.city',
                                    'users.state',
                                    'countries.name as user_country',

                                    'users.work_phone_code',
                                    'users.work_phone_code_city',
                                    'users.work_phone_number',
                                    'users.phone_code',
                                    'users.phone_number',
                                    'users.whatsapp_code',
                                    'users.whatsapp_number',
                                    'users.email',
                                    'users.cc_email',
                                    'users.solapin_name',
                                    'users.solapin_lastname',

                                    'category_inscriptions.name as category', 
                                    'inscriptions.special_code',
                                    'inscriptions.price_category',
                                    'inscriptions.total', 
                                    'inscriptions.payment_method', 
                                    'inscriptions.status', 
                                    'inscriptions.created_at',
                                    'inscriptions.assistance',
                                    'inscriptions.assistance_accomp')
                            ->leftjoin('category_inscriptions', 'category_inscriptions.id', '=', 'inscriptions.category_inscription_id')
                            ->leftjoin('countries as nationalities', 'nationalities.id', '=', 'users.nationality')
                            ->leftjoin('countries', 'countries.id', '=', 'users.country')
                            ->where('inscriptions.status', '!=', 'Refused')
                            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Salutation',
            'First Name',
            'Middle Name',
            'Last Name',
            'Degrees',
            'CUGH Member',
            'Document Type',
            'Document Number',
            'Nationality',
            'Gender',
            'Occupation',

            'Workplace',
            'Workplace Address',
            'City',
            'State',
            'Country',
            
            'Work Phone',
            'Cell Phone',
            'WhatsApp',
            'E-mail',
            'Cc E-mail',
            'Solapin Name',
            'Solapin Last Name',

            'Category',
            'Price Category',
            'Special Code',
            'Total Payment',
            'Payment Method',
            'Status',
            'Registration Date',
        ];
    }

    public function map($inscription): array
    {
        return [
            $inscription->id,
            $inscription->salutation,
            $inscription->name,
            $inscription->lastname,
            $inscription->second_lastname,
            $inscription->degrees,
            $inscription->is_cugh_member,
            $inscription->document_type,
            $inscription->document_number,
            $inscription->user_nationality,
            $inscription->gender,
            $inscription->occupation,

            $inscription->workplace,
            $inscription->address,
            $inscription->city,
            $inscription->state,
            $inscription->user_country,

            $inscription->work_phone_code.' '.$inscription->work_phone_code_city.' '.$inscription->work_phone_number,
            $inscription->phone_code.' '.$inscription->phone_number,
            $inscription->whatsapp_code.' '.$inscription->whatsapp_number,
            $inscription->email,
            $inscription->cc_email,
            $inscription->solapin_name,
            $inscription->solapin_lastname,


            $inscription->category,
            $inscription->price_category,
            $inscription->special_code,
            $inscription->total,
            $inscription->payment_method,
            $inscription->status,
            $inscription->created_at,

        ];
    }

    public function styles(Worksheet $sheet){
        $sheet->getStyle('A1:AE1')->applyFromArray([
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
        $sheet->getColumnDimension('B')->setWidth(11);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(7);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(14);
        $sheet->getColumnDimension('K')->setWidth(7);
        $sheet->getColumnDimension('L')->setWidth(13);
        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(18);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(16);
        $sheet->getColumnDimension('S')->setWidth(16);
        $sheet->getColumnDimension('T')->setWidth(16);
        $sheet->getColumnDimension('U')->setWidth(25);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(20);
        $sheet->getColumnDimension('X')->setWidth(25);
        $sheet->getColumnDimension('Y')->setWidth(22);
        $sheet->getColumnDimension('Z')->setWidth(14);
        $sheet->getColumnDimension('AA')->setWidth(17);
        $sheet->getColumnDimension('AB')->setWidth(14);
        $sheet->getColumnDimension('AC')->setWidth(22);
        $sheet->getColumnDimension('AD')->setWidth(22);
        $sheet->getColumnDimension('AE')->setWidth(22);

    }

}
