<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Inscription;

class ExporInscriptions implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
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
                                    'inscriptions.invoice_type',
                                    'inscriptions.voucher_file',
                                    'inscriptions.compr_pdf',
                                    'inscriptions.created_at',

                                    'payments.card_number',
                                    'payments.transaction_date',
                                    'payments.currency',
                                    'payments.amount',
                                    'payments.amount',
                                    'payments.status_payment')
                            ->leftjoin('category_inscriptions', 'category_inscriptions.id', '=', 'inscriptions.category_inscription_id')
                            ->leftjoin('countries as nationalities', 'nationalities.id', '=', 'users.nationality')
                            ->leftjoin('countries', 'countries.id', '=', 'users.country')
                            ->leftjoin('payments', 'payments.inscription_id', '=', 'inscriptions.id')
                            ->where('inscriptions.status', '!=', 'Refused')
                            ->orderByRaw("
                                FIELD(inscriptions.status,
                                    'Confirmed',
                                    'Paid',
                                    'Processing',
                                    'Pending',
                                    'Draft'
                                )
                            ")
                            ->orderBy('inscriptions.created_at', 'asc')
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
            'Payment Information',
            'Status',
            'Invoice Type',
            'Invoice',
            'Registration Date',
        ];
    }

    public function map($inscription): array
    {

        //if credit card, show card number
        if ($inscription->payment_method == 'Credit/Debit Card') {
            $payment_info  = $inscription->card_number . ' (' . $inscription->transaction_date . '-' . $inscription->currency . ' ' . $inscription->amount . ' | ' . $inscription->status_payment . ')';
        }else{
            $payment_info = '';
        }


        return [
            $inscription->id,
            $inscription->salutation,
            $inscription->name,
            $inscription->lastname,
            $inscription->second_lastname,
            $inscription->degrees,
            $inscription->is_cugh_member ? 'Yes' : 'No',
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
            $payment_info,
            $inscription->status,
            $inscription->invoice_type,
            $inscription->compr_pdf,
            $inscription->created_at,

        ];
    }

    public function styles(Worksheet $sheet){
        $sheet->getStyle('A1:AH1')->applyFromArray([
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
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(11);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(7);
        $sheet->getColumnDimension('H')->setWidth(16);
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
        $sheet->getColumnDimension('AD')->setWidth(60);
        $sheet->getColumnDimension('AE')->setWidth(11);
        $sheet->getColumnDimension('AF')->setWidth(12);
        $sheet->getColumnDimension('AG')->setWidth(35);
        $sheet->getColumnDimension('AH')->setWidth(19);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $lastRow; $row++) {

                    // Cambia AE por la columna donde está el Status
                    $status = trim((string) $sheet->getCell("AE{$row}")->getValue());

                    switch ($status) {
                        case 'Confirmed':
                            $color = 'ddf5f0';      // Fondo
                            $fontColor = '00ab55';  // Texto
                            break;

                        case 'Paid':
                            $color = 'f2eafa';      // Fondo
                            $fontColor = 'F2A413';  // Texto
                            break;

                        case 'Processing':
                            $color = 'e6f4ff';      // Fondo
                            $fontColor = '2196f3';  // Texto
                            break;

                        case 'Pending':
                            $color = 'fcf5e9';      // Fondo
                            $fontColor = 'e2a03f';  // Texto
                            break;

                        case 'Draft':
                            $color = 'eceffe';      // Fondo
                            $fontColor = 'CC1F2F';  // Texto
                            break;

                        default:
                            continue 2; // Saltar a la siguiente fila
                    }

                    $sheet->getStyle("A{$row}:AH{$row}")
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => $color,
                            ],
                        ],
                        'font' => [
                            'bold' => true, // Opcional
                            'color' => [
                                'argb' => $fontColor,
                            ],
                        ],
                    ]);
                }

                // Aplicar bordes a toda la tabla
                $sheet->getStyle("A1:AH{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => [
                                    'argb' => 'D9D9D9',
                                ],
                            ],
                        ],
                    ]);

            },
        ];
    }

}
