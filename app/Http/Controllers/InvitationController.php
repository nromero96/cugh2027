<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Country;
use App\Mail\InvitationEmail;

//log
use Illuminate\Support\Facades\Log;

use TCPDF;
use Carbon\Carbon;

class InvitationController extends Controller
{
    //index
    public function index()
    {
        //name
        // $category_name = '';
        $data = [
            'category_name' => 'invitations',
            'page_name' => 'invitations',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get invitations
        $invitations = Invitation::orderBy('id', 'desc')->get();

        return view('pages.invitatios.index', $data, compact('invitations'));
    }

    public function showOnlineForm_invitations()
    {

        //get countries
        $countries = Country::orderBy('name', 'asc')->get();

        return view('pages.invitatios.onlineform')->with('countries', $countries);
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone_code' => 'required|string',
            'phone' => 'required|string',
            'country' => 'required|string',
        ]);

        $errors = [];

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422); // 422 es el código de estado para errores de validación
        }

        $invitation = Invitation::create($request->all());

        $pdfFilePath = $this->generateInvitationPDF($invitation);
        $this->sendInvitationEmail($invitation->email, $pdfFilePath, $invitation->full_name, $invitation->country);

        return response()->json(['message' => 'Invitation generated and sent successfully']);
    }


    private function generateInvitationPDF(Invitation $invitation)
    {
        
        //GET path logo and firma
        $logo = public_path('assets/img/logo.png');
        $firma = public_path('assets/img/firma-dr-gustavo-camino.png');

        // Establecer la zona horaria
        date_default_timezone_set('America/Lima');

        // Obtener la fecha actual
        $fechaactual = Carbon::now()->locale('es_PE')->isoFormat('DD [\de] MMMM [\de] YYYY');

        // Agregar "Lima," al inicio
        $fechaactual = 'Lima, ' . $fechaactual;

        $content = <<<EOD
            <p style="font-size:15px;text-align:center; color:#c40000;"><img src="{$logo}" alt="logo" width="80" height="80" /><br><b>XLI REUNIÓN ANUAL DE DERMATÓLOGOS LATINOAMERICANOS</b><br><b style="color:#000;font-size:13px;text-align:center;">Swissôtel Lima, 8 al 11 de Mayo de 2024</b><br><br></p>
            <p>{$fechaactual}</p>
            <p>Señor(a) Doctor(a)</p>
            <p><strong>{$invitation->full_name}</strong></p>
            <p><strong>E-mail:</strong> {$invitation->email}</p>
            <p><strong>País:</strong> {$invitation->country}</p>
            <p>Estimado(a) colega:</p>
            <p style="text-align: justify;">Es grato dirigirme a usted para invitarle muy cordialmente a participar en la <b>XLI Reunión Anual de Dermatólogos Latinoamericanos</b> que se realizará en la ciudad de Lima, del 8 al 11 de mayo de 2026.</p>
            <p style="text-align: justify;">El evento dermatológico más importante de la región congregará en Lima a más de 2,500 profesionales especialistas y residentes en dermatología, procedentes de los países que integran CUGH 2027, de América, Europa y Asia Pacífico.  Esperamos que esta invitación encuentre en Ud. favorable acogida que le permita disfrutar de un congreso con alta calidad científica con la presencia de destacados profesores internacionales especialmente invitados para la ocasión.</p>
            <p>Hacemos propicia esta oportunidad para reiterarle nuestros más cordiales saludos.</p>
            <br>
            <p>Atentamente,</p>
            <p><img src="{$firma}" alt="logo" width="120" /><br><b>Dr. Gustavo Camino</b><br>Presidente<br>CUGH 2027</p>
            <br>
            <br>
            <p style="font-size:10px;text-align:center;">Nota: Esta invitación es exclusiva para inscribirse en CUGH 2027 y no incluye gastos de viaje a Perú:<br>
            pasaje aéreo, hospedaje o traslados en Lima.
            </p>
            <p style="font-size:10px;text-align:center; color:blue;"><b>Secretaría & Organización: Tel. (51 1) 983481269 - 998672199</b><br>
            E-mail: inscripciones@rosmarasociados.com 
            </p>
        EOD;

        // Set up the PDF content using TCPDF methods
        $pdf = new TCPDF();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 11);
        $pdf->writeHTML($content, true, false, true, false, '');

        // Save the PDF to the specified directory
        $pdfFilePath = storage_path('app/public/uploads/invitation_letters/') . 'invitation_' . time() . '.pdf';
        $pdf->Output($pdfFilePath, 'F');

        // Update the invitation record with the file name
        $invitation->update(['file_name' => basename($pdfFilePath)]);

        return $pdfFilePath;
    }

    private function sendInvitationEmail($email, $pdfFilePath, $fullName, $country)
    {
        // Send the email with attachment using Laravel's Mail service add copied to
        \Mail::to($email)->cc(config('services.correonotificacion.inscripcion'))->send(new InvitationEmail($pdfFilePath, $fullName, $country));

    }

}
