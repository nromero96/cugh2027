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

}
