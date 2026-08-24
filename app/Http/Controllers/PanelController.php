<?php

namespace App\Http\Controllers;

use App\Models\Panel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;

use Illuminate\Support\Facades\Mail;
use App\Mail\PanelSubmissionMail;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $panels = Panel::all();

        return view('pages.panels.index', $data)->with('panels', $panels);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $countries = Country::all();

        return view('pages.panels.create', $data)->with('countries', $countries);
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
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function show(Panel $panel)
    {
        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $panel = Panel::find($panel->id);

        return view('pages.panels.show', $data)->with('panel', $panel);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function edit(Panel $panel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Panel $panel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Panel $panel)
    {
        //
    }


    public function formOnline()
    {
        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $countries = Country::all();

        return view('pages.panels.form-online', $data)->with('countries', $countries);
    }

    public function storeOnline(Request $request)
    {
        $request->validate([

            'language' => 'required',

            'subthemes' => 'nullable|array|max:3',
            'subthemes.*' => 'string',

            'title' => 'required|max:150',

            'contact_name' => 'required',
            'contact_email' => 'required|email',

            'description' => 'required|max:2000',
            'learning_objectives' => 'required|max:2000',
        ]);

        // Limpiar speakers vacíos
        $speakers = [];

        if ($request->has('speakers')) {

            foreach ($request->speakers as $speaker) {

                // evitar guardar filas vacías
                if (
                    empty($speaker['name']) &&
                    empty($speaker['position']) &&
                    empty($speaker['institution']) &&
                    empty($speaker['country'])
                ) {
                    continue;
                }

                $speakers[] = [
                    'name' => $speaker['name'] ?? null,
                    'position' => $speaker['position'] ?? null,
                    'institution' => $speaker['institution'] ?? null,
                    'country' => $speaker['country'] ?? null,
                ];
            }
        }

        $panel = Panel::create([

            'language' => $request->language,

            'subthemes' => $request->subthemes,
            'subthemes_other' => $request->subthemes_other,

            'title' => $request->title,

            'contact_salutation' => $request->contact_salutation,
            'contact_name' => $request->contact_name,
            'contact_institution' => $request->contact_institution,
            'contact_country' => $request->contact_country,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,

            'moderator_name' => $request->moderator_name,
            'moderator_position' => $request->moderator_position,
            'moderator_institution' => $request->moderator_institution,
            'moderator_country' => $request->moderator_country,

            'speakers' => $speakers,

            'description' => $request->description,
            'learning_objectives' => $request->learning_objectives,
        ]);

        // SEND EMAIL
        Mail::to($request->contact_email)
            ->bcc(config('services.correonotificacion.panel'))
            ->send(new PanelSubmissionMail($panel));

        return redirect()
            ->back()
            ->with('success', 'Panel submitted successfully.');
    }

    public function pdf(Panel $panel)
    {

        // 🔒 Validar que sea el Admistrador
        if (!\Auth::user()->hasRole('Administrador')) {
            abort(403);
        }

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Panel N° ' . $panel->id);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $subthemes_list = '';
        // 1. Convertir la cadena JSON de la base de datos a un array de PHP
        $subthemes = is_string($panel->subthemes) ? json_decode($panel->subthemes, true) : $panel->subthemes;

        if (!empty($subthemes) && is_array($subthemes)) {
            $items = [];
            foreach ($subthemes as $subtheme) {
                $items[] = '<span><b>*</b> ' . htmlspecialchars($subtheme) . '</span>';
            }
            $subthemes_list = implode('<br>', $items);
        }


        $speakers_list = '';

        // 1. Convertir la cadena JSON de la base de datos a un array de PHP
        $speakers = is_string($panel->speakers) ? json_decode($panel->speakers, true) : $panel->speakers;

        if (!empty($speakers) && is_array($speakers)) {
            $items = [];
            $num = 1;

            foreach ($speakers as $speaker) {
                // Evita errores "Undefined index" usando el operador nulo seguro (??)
                $name = htmlspecialchars($speaker['name'] ?? '');
                $position = htmlspecialchars($speaker['position'] ?? '');
                $institution = htmlspecialchars($speaker['institution'] ?? '');
                $country = htmlspecialchars($speaker['country'] ?? '');

                $items[] = '<tr>
                                <td width="15" style="text-align: center; background-color: #e6e6e6;"><br><br><br>' . $num++ . '</td>
                                <td>
                                    <span><b>Name: </b> ' . $name . '</span><br>
                                    <span><b>Position: </b> ' . $position . '</span><br>
                                    <span><b>Institution: </b> ' . $institution . '</span><br>
                                    <span><b>Country: </b> ' . $country . '</span>
                                </td>
                            </tr>';
            }

            // 2. Unir los elementos fuera del bucle foreach
            $speakers_list = implode('', $items);
        }

        $html = '
        <style>
            .title {
                font-size: 16px;
                font-weight: bold;
                
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
                margin-bottom: 0px;  
            }
        </style>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td class="title">Panel N° ' . $panel->id . '</td></tr>
        </table>
        <br />
        <div class="item"><span class="label">Language:</span><br /><span class="value">' . e($panel->language) . '</span></div>
        <div class="item"><span class="label">Sub-Themes:</span><br /><span class="value">' .  $subthemes_list . '</span></div>
        <div class="item"><span class="label">Title:</span><br /><span class="value">' . e($panel->title) . '</span></div>
        <h3>Contact person:</h3>
        <b>Salutation: </b><span>' . e($panel->contact_salutation).'</span><br><b>Name: </b><span>' . e($panel->contact_name) . '</span><br><b>Institution:</b> <span>' . e($panel->contact_institution) . '</span><br><b>Country:</b> <span>' . e($panel->contact_country) . '</span><br><b>Phone:</b> <span>' . e($panel->contact_phone) . '</span><br><b>E-mail:</b> <span>' . e($panel->contact_email) . '</span>
        <h3>Moderator:</h3>
        <b>Name: </b><span>' .e($panel->moderator_name). '</span><br><b>Position: </b><span>' .e($panel->moderator_position). '</span><br><b>Institution: </b><span>' .e($panel->moderator_institution). '</span><br><b>Country: </b><span>' .e($panel->moderator_country). '</span><br>
        <h3>Speakers:</h3>
        <table border="1" cellspacing="0" cellpadding="2" width="950">' . $speakers_list . '</table>
        <br>
        <div class="item"><span class="label">Panel Description:</span><br /><span class="value">' . nl2br(e($panel->description)) . '</span></div>
        <div class="item"><span class="label">Learning Objectives:</span><br /><span class="value">' . nl2br(e($panel->learning_objectives)) . '</span></div>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('panel.pdf', 'I'))->header('Content-Type', 'application/pdf');

        

       
    }

}
