<?php

namespace App\Services;

class AbstractAcceptanceLetterPdf
{
    public function render(array $letterData, int $abstractId): string
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor('CUGH Lima 2027 Conference Secretariat');
        $pdf->SetTitle('Abstract acceptance notification N° '.$abstractId);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(23, 18, 20);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        $logoWidth = 31;
        $pdf->Image(public_path('assets/img/logo2.png'), ($pdf->getPageWidth() - $logoWidth) / 2, 14, $logoWidth, $logoWidth, 'PNG');
        $pdf->SetY(51);
        $pdf->SetFont('helvetica', '', 9.7);
        $pdf->writeHTML(view('pdf.abstract-acceptance', $letterData)->render(), true, false, true, false, '');

        return $pdf->Output('abstract-acceptance-'.$abstractId.'.pdf', 'S');
    }
}
