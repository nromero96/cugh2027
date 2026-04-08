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
            'job_position' => 'required|string',
            'institution' => 'required|string',
            'passport_number' => 'required|string',
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
        $background = public_path('assets/img/bg-letter-invitation.jpg');

        $logo = public_path('assets/img/logo.png');
        $firma = public_path('assets/img/firma-dr-gustavo-camino.png');

        // Establecer la zona horaria
        date_default_timezone_set('America/Lima');

        // Obtener la fecha actual
        $fechaactual = Carbon::now()->locale('es_PE')->isoFormat('DD [\de] MMMM [\de] YYYY');

        // Agregar "Lima," al inicio
        $fechaactual = 'Lima, ' . $fechaactual;

        $content_date = <<<EOD
            <p style="text-align: left;">{$fechaactual}</p>
        EOD;

        $content_numeber = <<<EOD
            <p style="text-align: left;">Carta N° {$invitation->id} – CUGH2027</p>
        EOD;

        $content = <<<EOD
            <p>Señor(a) Doctor(a)</p>
            <p><strong>{$invitation->full_name}</strong><br><strong>{$invitation->job_position}</strong><br><strong>{$invitation->institution}</strong><br><strong>Pasaporte Nº:</strong> {$invitation->passport_number}<br><strong>E-mail:</strong> {$invitation->email}<br><strong>País:</strong> {$invitation->country}</p>
            <p>Estimado(a) colega:</p>
            <p style="text-align: justify;">Es grato dirigirme a usted para invitarle muy cordialmente a participar en la <b><i>18<sup>th</sup> Annual Conference CUGH 2027 Transforming Global Health:</i></b> Partnerships, power, leadership and technology in a rapidly changing world; conferencia que se realizará en la ciudad de Lima, del 25 al 28 de febrero de 2027.</p>
            <p style="text-align: justify;">Este evento es un hito histórico, ya que será la primera vez que la Conferencia Anual del Consortium of Universities for Global Health (CUGH) se celebre fuera de los Estados Unidos, lo que ofrece una oportunidad para continuar trabajando en los temas de la salud global, destacando los retos e innovaciones de la región de América Latina y el Caribe, sobre todo del Perú.</p>
            <p style="text-align: justify;">La presente invitación se emite con el fin de apoyar su solicitud de visa para ingresar al Perú. Cabe señalar que los organizadores del evento no asumirán responsabilidad financiera por gastos de viaje, alojamiento u otros relacionados con su visita.<br>Esperamos que esta invitación encuentre en Ud. favorable acogida que le permita disfrutar de un encuentro con profesionales, investigadores y líderes en salud global de todo el mundo, con el objetivo de intercambiar conocimientos y fortalecer la colaboración internacional. </p>
            <p>Hacemos propicia esta oportunidad para reiterarle nuestros más cordiales saludos.</p>
            <br>
            <p>Atentamente,</p>
            <p><img src="{$firma}" alt="logo" width="120" /><br>Patricia J. García, MD, MPH, PhD<br><b style="font-size: 8px;">Presidenta<br><i>18<sup>th</sup> Annual Conference CUGH 2027<br>Transforming Global Health:</i></b><br><i style="font-size: 8px;">Partnerships, power, leadership and technology in a rapidly changing world</i></p>
        EOD;

        // Set up the PDF content using TCPDF methods
        $pdf = new TCPDF();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Ajustar márgenes a 0 para que la imagen de fondo ocupe toda la página
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);

        $pdf->AddPage();
        // Fondo ocupa toda la página
        $pdf->Image(
            $background,
            0, 0,
            $pdf->getPageWidth(),
            $pdf->getPageHeight(),
            '', '', '', false, 300, '', false, false, 0
        );
        $pdf->SetFont('helvetica', '', 9);

        // Fecha arriba a la derecha
        $pdf->writeHTMLCell(
            50,        // ancho del bloque
            0,         // altura automática
            150,       // X en mm desde izquierda
            60,        // Y en mm desde arriba
            $content_date, 
            0,         // borde 0
            0,         // salto de línea 0 = siguiente contenido continua
            false,     // fill
            true,      // reset height
            'R',       // alineación dentro del bloque
            true       // autocells
        );

        // Carta N° arriba a la izquierda
        $pdf->writeHTMLCell(
            50,
            0,
            22,
            60,
            $content_numeber,
            0,
            0,
            false,
            true,
            'L',
            true
        );

        // Cuerpo de la carta
        $pdf->writeHTMLCell(
            170,        // ancho = usa todo disponible dentro de los márgenes
            0,
            22,
            70,
            $content,
            0,
            1,        // salto de línea después
            false,
            true,
            'J',
            true
        );

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
