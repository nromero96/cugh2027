<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;


class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category_name' => 'workshops',
            'page_name' => 'workshops',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get invitations
        $workshops = Workshop::orderBy('id', 'desc')->get();

        return view('pages.workshops.index', $data, compact('workshops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function show(Workshop $workshop)
    {

        //solo el administrador puede ver el workshop
        if (!auth()->user()->hasRole('Administrador')) {
            return redirect()->route('workshops.index')
                ->with('error', 'You do not have permission to view this workshop.');
        }

        $data = [
            'category_name' => 'workshops',
            'page_name' => 'workshops_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $workshop = Workshop::find($workshop->id);

        return view('pages.workshops.show', $data, compact('workshop'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function edit(Workshop $workshop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workshop $workshop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Workshop $workshop)
    {
        //
    }


    public function registerWorkshop(Request $request)
    {
        return view('pages.workshops.form-online');
    }

    public function storeWorkshop(Request $request)
    {
    
        $request->validate([

            // Lead
            'lead_name' => 'required|string|max:255',
            'lead_institution' => 'required|string|max:255',
            'lead_title' => 'required|string|max:255',
            'lead_email' => 'required|email|max:255',
            'lead_phone' => 'required|string|max:255',
            'lead_cell' => 'required|string|max:255',

            // Workshop
            'workshop_title' => 'required|string|max:255',
            'workshop_desc' => 'required|string',
            'workshop_objectives' => 'required|string',
            'workshop_speakers' => 'nullable|string',

            // Room
            'time_slot' => 'required|string',
            'day_length' => 'required|string',
            'room_setup' => 'required|string',
            'attendees' => 'required|integer|min:1',
            'notes' => 'nullable|string',

            // Payment
            'payment_lead_same' => 'required',

            // Terms
            'terms' => 'required',

            // Signature
            'signature' => 'required',
            'place_date' => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardar firma
        |--------------------------------------------------------------------------
        */

        $signature = $request->signature;

        if (!$signature) {
            dd('No llegó la firma');
        }

        // quitar encabezado
        $signature = preg_replace('/^data:image\/\w+;base64,/', '', $signature);

        $signature = str_replace(' ', '+', $signature);

        $imageData = base64_decode($signature);

        if ($imageData === false) {
            dd('Base64 inválido');
        }

        // nombre archivo
        $fileName = Str::uuid() . '.png';

        // carpeta destino
        $folderPath = public_path('storage/uploads/workshops');

        // crear carpeta si no existe
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // ruta completa
        $fullPath = $folderPath . '/' . $fileName;

        // guardar archivo
        if (file_put_contents($fullPath, $imageData) === false) {

            dd([
                'folderPath' => $folderPath,
                'fullPath' => $fullPath,
                'folder_exists' => is_dir($folderPath),
                'folder_writable' => is_writable($folderPath),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Crear workshop
        |--------------------------------------------------------------------------
        */

        Workshop::create([

            // Lead
            'lead_name' => $request->lead_name,
            'lead_institution' => $request->lead_institution,
            'lead_title' => $request->lead_title,
            'lead_email' => $request->lead_email,
            'lead_phone' => $request->lead_phone,
            'lead_cell' => $request->lead_cell,

            // Workshop
            'workshop_title' => $request->workshop_title,
            'workshop_desc' => $request->workshop_desc,
            'workshop_objectives' => $request->workshop_objectives,
            'workshop_speakers' => $request->workshop_speakers,

            // Room
            'time_slot' => $request->time_slot,
            'day_length' => $request->day_length,
            'room_setup' => $request->room_setup,
            'attendees' => $request->attendees,
            'notes' => $request->notes,

            // Payment
            'payment_lead_same' => $request->payment_lead_same === 'Yes',

            'payment_name' => $request->payment_name,
            'payment_institution' => $request->payment_institution,
            'payment_title' => $request->payment_title,
            'payment_email' => $request->payment_email,
            'payment_phone' => $request->payment_phone,
            'payment_cell' => $request->payment_cell,

            // Signature
            'signature_path' => $fileName,

            'place_date' => $request->place_date,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Workshop application submitted successfully.');

    }


    public function pdf(Workshop $workshop)
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Workshop N° ' . $workshop->id);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $signature = '';

        if (!empty($workshop->signature_path)) {
            $signaturePath = public_path('storage/uploads/workshops/' . $workshop->signature_path);

            if (file_exists($signaturePath)) {
                $signature = '<br><img src="' . $signaturePath . '" width="220"><br>';
            }
        }

        $html = '
        <style>
            h2 {
                font-size: 16px;
                
            }

            h4 {
                font-size: 12px;
            }

            .label {
                font-weight: bold;
                color: #000000;
            }

            .value {
                color: #333333;
                line-height: 1.5;
            }

            .item {
                margin-bottom: 8px;
            }

            hr {
                border: 0.5px solid #dddddd;
            }
        </style>

        <h2>Workshop N°: ' . e($workshop->id) . '</h2>
        <p><span class="label">Created:</span> ' . e($workshop->created_at) . '</p>

        <h4>Lead Contact Person</h4>

        <div class="item"><span class="label">Name:</span><br><span class="value">' . e($workshop->lead_name) . '</span></div>

        <div class="item"><span class="label">Institution:</span><br><span class="value">' . e($workshop->lead_institution) . '</span></div>

        <div class="item"><span class="label">Professional Title:</span><br><span class="value">' . e($workshop->lead_title) . '</span></div>

        <div class="item"><span class="label">E-mail:</span><br><span class="value">' . e($workshop->lead_email) . '</span></div>

        <div class="item"><span class="label">Phone number (with country & area code):</span><br><span class="value">' . e($workshop->lead_phone) . '</span></div>

        <div class="item"><span class="label">Cell Phone (with country code):</span><br><span class="value">' . e($workshop->lead_cell) . '</span></div>

        <h4>Workshop Program Description</h4>

        <div class="item"><span class="label">Workshop Title:</span><br><span class="value">' . e($workshop->workshop_title) . '</span></div>

        <div class="item"><span class="label">Workshop Description (max 200 words):</span><br><span class="value">' . nl2br(e($workshop->workshop_desc)) . '</span></div>

        <div class="item"><span class="label">Objectives / Skills:</span><br><span class="value">' . nl2br(e($workshop->workshop_objectives)) . '</span></div>

        <div class="item"><span class="label">Speakers and Facilitators:</span><br><span class="value">' . nl2br(e($workshop->workshop_speakers)) . '</span></div>

        <h4>Workshop Room Options</h4>

        <div class="item"><span class="label">Preferred Time Slot:</span><br><span class="value">' . e($workshop->time_slot) . '</span></div>

        <div class="item"><span class="label">Half or Full Day:</span><br><span class="value">' . e($workshop->day_length) . '</span></div>

        <div class="item"><span class="label">Preferred Room Set-up:</span><br><span class="value">' . e($workshop->room_setup) . '</span></div>

        <div class="item"><span class="label">Desired Number of Attendees:</span><br><span class="value">' . e($workshop->attendees) . '</span></div>

        <div class="item"><span class="label">Notes or Comments:</span><br><span class="value">' . nl2br(e($workshop->notes)) . '</span></div>

        <h4>Contact for Invoice</h4>

        <div class="item"><span class="label">Will the applying party be the lead contact person for payment?:</span><br><span class="value">' . ($workshop->payment_lead_same ? 'Yes' : 'No') . '</span></div>

        <br><br>

        <div class="item">
            ' . $signature . '
            <span class="value">' . e($workshop->place_date) . '</span>
        </div>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('workshop-' . $workshop->id . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

}
