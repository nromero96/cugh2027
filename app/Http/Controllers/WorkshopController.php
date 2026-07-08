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

}
