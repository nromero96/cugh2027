<?php

namespace App\Services;

class AbstractRejectionLetterPdf
{
    public function render(array $letterData, int $abstractId): string
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor('CUGH Lima 2027 Conference Secretariat');
        $pdf->SetTitle('Abstract rejection notification #'.sprintf('%06d', $abstractId));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(25, 25, 20);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        // A slim border echoes the colors of the conference logo.
        $borderColors = [
            [237, 0, 140],  // magenta
            [237, 28, 36],  // red
            [119, 191, 67], // green
            [247, 148, 30], // orange
            [255, 242, 0],  // yellow
        ];
        foreach ($borderColors as $index => $color) {
            $pdf->SetFillColor(...$color);
            $pdf->Rect(9, 17 + ($index * 52), 2.5, 52, 'F');
        }

        $logoWidth = 36;
        $pdf->Image(public_path('assets/img/logo2.png'), ($pdf->getPageWidth() - $logoWidth) / 2, 17, $logoWidth, $logoWidth, 'PNG');

        $pdf->SetY(60);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->writeHTML(view('pdf.abstract-rejection', $letterData)->render(), true, false, true, false, '');

        return $pdf->Output('abstract-rejection-'.sprintf('%06d', $abstractId).'.pdf', 'S');
    }
}
