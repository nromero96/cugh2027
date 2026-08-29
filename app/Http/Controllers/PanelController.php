<?php

namespace App\Http\Controllers;

use App\Models\Panel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Mail\PanelSubmissionMail;
use App\Exports\PanelExport;
use Maatwebsite\Excel\Facades\Excel;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $search = trim((string) $request->input('search', ''));
        $panelsQuery = Panel::query()->latest('created_at');

        if ($search !== '') {
            $idSearch = ltrim($search, '#');
            $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

            $panelsQuery->where(function ($query) use ($idSearch, $terms) {
                if (ctype_digit($idSearch)) {
                    $query->where('id', (int) $idSearch);
                }

                $textSearch = function ($textQuery) use ($terms) {
                    foreach ($terms as $term) {
                        $escapedTerm = addcslashes(mb_strtolower($term, 'UTF-8'), '%_\\');
                        $like = "%{$escapedTerm}%";

                        $textQuery->where(function ($fieldQuery) use ($like) {
                            $fieldQuery
                                ->whereRaw('LOWER(title) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(contact_name) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(contact_email) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(contact_institution) LIKE ?', [$like]);
                        });
                    }
                };

                if (ctype_digit($idSearch)) {
                    $query->orWhere($textSearch);
                } else {
                    $query->where($textSearch);
                }
            });
        }

        $panels = $panelsQuery->get();

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
        $this->ensureAdministrator();

        $data = [
            'category_name' => 'panels',
            'page_name' => 'panels_edit',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        return view('pages.panels.edit', $data)
            ->with('panel', $panel)
            ->with('countries', Country::orderBy('name')->get());
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
        $this->ensureAdministrator();
        $this->validatePanelRequest($request);

        $speakers = $this->cleanSpeakers($request->input('speakers', []));

        try {
            $panel->update($this->panelData($request, $speakers));
        } catch (\Throwable $exception) {
            Log::error('Panel administrative update failed.', [
                'panel_id' => $panel->id,
                'administrator_id' => auth()->id(),
                'exception' => $exception,
            ]);

            return back()->withInput()->with('error', 'We could not update the panel. Please try again.');
        }

        return redirect()->route('panels.show', $panel)
            ->with('success', 'Panel updated successfully.');
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
        $this->validatePanelRequest($request);

        $speakers = $this->cleanSpeakers($request->input('speakers', []));

        try {
            $panel = Panel::create($this->panelData($request, $speakers));
        } catch (\Throwable $exception) {
            Log::error('Panel submission could not be saved.', [
                'contact_email' => $request->contact_email,
                'exception' => $exception,
            ]);

            return back()->withInput()->with('error', 'We could not save your panel submission. Please try again.');
        }

        // SEND EMAIL
        try {
            Mail::to($request->contact_email)
                ->bcc(config('services.correonotificacion.panel'))
                ->send(new PanelSubmissionMail($panel));
        } catch (\Throwable $exception) {
            Log::error('Panel submission confirmation email failed.', [
                'panel_id' => $panel->id,
                'contact_email' => $panel->contact_email,
                'exception' => $exception,
            ]);

            return redirect()->route('panels.formonline')->with(
                'success',
                'Panel submitted successfully. Your confirmation email could not be sent, but your submission was saved.'
            );
        }

        return redirect()->route('panels.formonline')
            ->with('success', 'Panel submitted successfully.');
    }

    private function validatePanelRequest(Request $request): void
    {
        $languages = [
            'English',
            'Spanish',
            'PPT Slides in English and Oral Presentation in Spanish',
        ];

        $subthemes = [
            'Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care',
            'Social Determinants of Health',
            'Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution',
            'Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases',
            'Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research',
            'Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions',
            'Other',
        ];

        $request->validate([
            'language' => ['required', Rule::in($languages)],
            'subthemes' => ['required', 'array', 'min:1', 'max:3'],
            'subthemes.*' => ['required', 'string', Rule::in($subthemes)],
            'subthemes_other' => [
                Rule::requiredIf(function () use ($request) {
                    return in_array('Other', $request->input('subthemes', []), true);
                }),
                'nullable',
                'string',
                'max:255',
            ],
            'title' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) {
                    preg_match_all("/[\\p{L}\\p{N}]+(?:['’\x{2010}-\x{2015}-][\\p{L}\\p{N}]+)*/u", strip_tags($value), $words);

                    if (count($words[0]) > 15) {
                        $fail('The title may not contain more than 15 words.');
                    }
                },
            ],
            'contact_salutation' => ['required', Rule::in(['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'])],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_institution' => ['nullable', 'string', 'max:255'],
            'contact_country' => ['nullable', 'string', Rule::exists('countries', 'name')],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['required', 'email:rfc', 'max:255'],
            'moderator_name' => ['nullable', 'string', 'max:255'],
            'moderator_position' => ['nullable', 'string', 'max:255'],
            'moderator_institution' => ['nullable', 'string', 'max:255'],
            'moderator_country' => ['nullable', 'string', Rule::exists('countries', 'name')],
            'speakers' => ['nullable', 'array', 'max:4'],
            'speakers.*' => ['array'],
            'speakers.*.name' => ['nullable', 'string', 'max:255'],
            'speakers.*.position' => ['nullable', 'string', 'max:255'],
            'speakers.*.institution' => ['nullable', 'string', 'max:255'],
            'speakers.*.country' => ['nullable', 'string', Rule::exists('countries', 'name')],
            'description' => ['required', 'string', 'max:2000'],
            'learning_objectives' => ['required', 'string', 'max:2000'],
        ], [
            'subthemes.required' => 'Please select at least one sub-theme.',
            'subthemes.max' => 'You may select up to 3 sub-themes.',
            'subthemes_other.required' => 'Please specify the other sub-theme.',
            'speakers.max' => 'You may add up to 4 speakers.',
        ]);

    }

    private function cleanSpeakers(array $submittedSpeakers): array
    {
        $speakers = [];

        foreach ($submittedSpeakers as $speaker) {
            if (empty(array_filter([
                $speaker['name'] ?? null,
                $speaker['position'] ?? null,
                $speaker['institution'] ?? null,
                $speaker['country'] ?? null,
            ]))) {
                continue;
            }

            $speakers[] = [
                'name' => $speaker['name'] ?? null,
                'position' => $speaker['position'] ?? null,
                'institution' => $speaker['institution'] ?? null,
                'country' => $speaker['country'] ?? null,
            ];
        }

        return $speakers;
    }

    private function panelData(Request $request, array $speakers): array
    {
        return [
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
        ];
    }

    private function ensureAdministrator(): void
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
    }

    public function exportExcel()
    {
        $this->ensureAdministrator();

        return Excel::download(
            new PanelExport(),
            'Panels_' . now()->format('Ymd_His') . '.xlsx'
        );
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
