<?php

namespace App\Http\Controllers;

use App\Models\AbstractPost;
use App\Models\AbstractPostNote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

use App\Exports\AbstractPostExport;
use Maatwebsite\Excel\Facades\Excel;

use TCPDF;

class AbstractPostController extends Controller
{
    private const MAX_ABSTRACTS_PER_PARTICIPANT = 3;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userid = auth()->id();

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $search = trim($request->input('search', ''));
        $status = $request->input('status');

        $query = AbstractPost::with('user');

        /*
        |--------------------------------------------------------------------------
        | Restricción por rol
        |--------------------------------------------------------------------------
        */

        if (
            !auth()->user()->hasRole('Administrador') &&
            !auth()->user()->hasRole('Secretaria')
        ) {
            $query->where('user_id', $userid);
        }

        /*
        |--------------------------------------------------------------------------
        | Buscador
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $idSearch = ltrim($search, '#');
                $isIdSearch = ctype_digit($idSearch);
                $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

                if ($isIdSearch) {
                    $subQuery->where('abstract_posts.id', (int) $idSearch);
                }

                $termSearch = function ($allTermsQuery) use ($terms) {
                    foreach ($terms as $term) {
                        $escapedTerm = addcslashes($term, '%_\\');
                        $like = "%{$escapedTerm}%";

                        $allTermsQuery->where(function ($fieldQuery) use ($like) {
                            $fieldQuery
                                ->where('abstract_posts.main_author->name', 'LIKE', $like)
                                ->orWhere('abstract_posts.main_author->lastname', 'LIKE', $like)
                                ->orWhere('abstract_posts.title', 'LIKE', $like)
                                ->orWhere('abstract_posts.presentation_type', 'LIKE', $like)
                                ->orWhere('abstract_posts.abstract_type', 'LIKE', $like)
                                ->orWhereHas('user', function ($userQuery) use ($like) {
                                    $userQuery->where('email', 'LIKE', $like);
                                });
                        });
                    }
                };

                if ($isIdSearch) {
                    $subQuery->orWhere($termSearch);
                } else {
                    $subQuery->where($termSearch);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filtro de estado
        |--------------------------------------------------------------------------
        */

        if (in_array($status, ['draft', 'submitted', 'accepted', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $abstract_posts = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $abstractCount = AbstractPost::where('user_id', $userid)->count();
        $abstractLimitReached = auth()->user()->hasRole('Participante')
            && $abstractCount >= self::MAX_ABSTRACTS_PER_PARTICIPANT;

        return view('pages.abstract_posts.index')
            ->with($data)
            ->with('abstract_posts', $abstract_posts)
            ->with('search', $search)
            ->with('status', $status)
            ->with('abstractCount', $abstractCount)
            ->with('abstractLimitReached', $abstractLimitReached)
            ->with('maxAbstracts', self::MAX_ABSTRACTS_PER_PARTICIPANT);

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = \Auth::user()->id;

        if ($this->participantAbstractLimitReached($id)) {
            return redirect()->route('abstract_posts.index')
                ->with('error', $this->abstractLimitMessage());
        }

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($id);

        $countries = Country::orderByRaw("CASE WHEN name = 'Perú' THEN 0 ELSE 1 END, name ASC")->get();

        return view('pages.abstract_posts.create')
            ->with($data)
            ->with('user', $user)
            ->with('countries', $countries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id_user = \Auth::user()->id;

        if ($this->participantAbstractLimitReached($id_user)) {
            return redirect()->route('abstract_posts.index')
                ->with('error', $this->abstractLimitMessage());
        }

        $action = $request->action;

        $keywords = json_decode($request->keywords ?? '[]', true);

        // ✅ VALIDACIÓN
        if ($action === 'submitted') {
            $request->validate([
                'main_author' => ['required', 'array'],
                'main_author.name' => ['required', 'string', 'max:100'],
                'main_author.lastname' => ['required', 'string', 'max:150'],
                'main_author_country_id' => ['required','integer','exists:countries,id'],
                'presentation_type' => 'required',
                'abstract_type' => 'required',
                'title' => 'required|max:250',
                'body' => 'required|max:3000',
            ]);

            // VALIDACIÓN DE CO-AUTHORS
            $coAuthorsNames = $request->co_authors_name ?? [];
            $coAuthorsLastnames = $request->co_authors_lastname ?? [];
            $hasCoAuthors = false;
            foreach ($coAuthorsNames as $i => $name) {
                $lastname = $coAuthorsLastnames[$i] ?? '';
                if ($name || $lastname) {
                    $hasCoAuthors = true;
                    break;
                }
            }

            if (!$hasCoAuthors) {
                return back()->withErrors([
                    'co_authors' => 'At least one co-author is required.'
                ])->withInput();
            }

            // VALIDACIÓN DE INSTITUTIONS
            $institutionsNames = $request->institutions ?? [];
            $hasInstitutions = false;
            foreach ($institutionsNames as $inst) {
                if ($inst) {
                    $hasInstitutions = true;
                    break;
                }
            }

            if (!$hasInstitutions) {
                return back()->withErrors([
                    'institutions' => 'At least one institution is required.'
                ])->withInput();
            }

            // VALIDACIÓN DE KEYWORDS
            if (!is_array($keywords) || count($keywords) < 1 || count($keywords) > 3) {
                return back()->withErrors([
                    'keywords' => 'Select between 1 and 3 keywords'
                ])->withInput();
            }

        }

        // =========================
        // MAIN AUTHOR
        // =========================
        $mainAuthor = [
            'name' => trim($request->input('main_author.name', '')),
            'lastname' => trim($request->input('main_author.lastname', '')),
        ];

        // =========================
        // 🔥 CO-AUTHORS (CON ID REAL)
        // =========================
        $coAuthors = [];
        foreach (($request->co_authors_name ?? []) as $i => $name) {
            $name = trim($name);
            $lastname = trim($request->co_authors_lastname[$i] ?? '');

            if ($name === '' && $lastname === '') {
                continue;
            }

            $id = $request->co_authors_id[$i] ?? 'ca_' . $i;

            $coAuthors[] = [
                'id' => $id,
                'name' => $name,
                'lastname' => $lastname,
            ];
        }

        // =========================
        // 🔥 INSTITUTIONS (usar IDs reales del frontend)
        // =========================
        $institutions = [];
        foreach (($request->institutions ?? []) as $i => $instName) {
            $instName = trim($instName);

            if ($instName === '') {
                continue;
            }

            $coauthorsIds = json_decode(
                $request->institution_coauthors[$i] ?? '[]',
                true
            );

            if (!is_array($coauthorsIds)) {
                $coauthorsIds = [];
            }

            $institutions[] = [
                'name' => $instName,
                'coauthors' => $coauthorsIds,
            ];
        }

        // =========================
        // 💾 GUARDAR
        // =========================
        $abstractpost = new AbstractPost();

        $abstractpost->user_id = $id_user;
        $abstractpost->presentation_type = $request->presentation_type;
        $abstractpost->abstract_type = $request->abstract_type;
        $abstractpost->subtopic = $request->subtopic;
        $abstractpost->title = $request->title;
        $abstractpost->body = $request->body;

        $abstractpost->status = $action;
        
        $abstractpost->main_author = $mainAuthor;
        $abstractpost->main_author_country_id = $request->main_author_country_id;
        $abstractpost->co_authors = $coAuthors;
        $abstractpost->institutions = $institutions;
        $abstractpost->keywords = $keywords;

        $abstractWasCreated = DB::transaction(function () use ($abstractpost, $id_user) {
            // Lock the participant row so simultaneous requests cannot exceed the limit.
            User::whereKey($id_user)->lockForUpdate()->first();

            if ($this->participantAbstractLimitReached($id_user)) {
                return false;
            }

            $abstractpost->save();

            return true;
        });

        if (!$abstractWasCreated) {
            return redirect()->route('abstract_posts.index')
                ->with('error', $this->abstractLimitMessage());
        }

        // =========================
        // 🔁 REDIRECCIÓN
        // =========================
        if ($action === 'draft') {
            return redirect()->route('abstract_posts.edit', $abstractpost->id)
                ->with('success', 'Draft saved');
        }

        return redirect()->route('abstract_posts.show', $abstractpost->id)
            ->with('success', 'Sent successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function show(AbstractPost $abstractPost)
    {

        // 🔒 Validar que sea el dueño o Admistrador
        if ($abstractPost->user_id != \Auth::id() && !\Auth::user()->hasRole('Administrador')) {
            abort(403);
        }

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($abstractPost->user_id);


        // ✅ validar que sea del usuario
        if ($abstractPost->user_id !== auth()->id() && !auth()->user()->hasRole(['Administrador', 'Calificador'])) {
            return redirect()->route('abstract_posts.index')
                ->with('error', 'Permission denied, you do not have permission to view this abstract post.');
        }

        // 🔥 cargar relación
        $abstractPost->load(['user', 'mainAuthorCountry', 'notes.user']);

        return view('pages.abstract_posts.show')->with($data)->with('abstract_post', $abstractPost)->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function edit(AbstractPost $abstractPost)
    {

        // 🔥 Validar que sea el dueño
        if ($abstractPost->user_id != \Auth::id()) {
            abort(403);
        }

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts_edit',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $userId = auth()->id();

        // ✅ validar que sea del usuario
        if ($abstractPost->user_id != $userId) {
            return redirect()->route('abstract_posts.index')
                ->with('error', 'No tienes permiso para editar este trabajo.');
        }

        // ✅ solo draft
        if ($abstractPost->status !== 'draft') {
            abort(403);
        }

        // 🔥 cargar relación
        $abstractPost->load('user');

        $countries = Country::orderByRaw("CASE WHEN name = 'Perú' THEN 0 ELSE 1 END, name ASC")->get();

        return view('pages.abstract_posts.edit')->with($data)->with('abstract_post', $abstractPost)->with('countries', $countries);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AbstractPost $abstractPost)
    {
        // 🔒 Validar que sea el dueño
        if ($abstractPost->user_id != \Auth::id()) {
            abort(403);
        }

        $action = $request->action;

        $keywords = json_decode($request->keywords ?? '[]', true);

        // Normalizar los saltos de línea del textarea
        $body = str_replace(
            ["\r\n", "\r"],
            "\n",
            $request->input('body', '')
        );

        $request->merge([
            'body' => $body,
        ]);

        // ✅ VALIDACIÓN
        if ($action === 'submitted') {
            $request->validate([
                'main_author' => ['required', 'array'],
                'main_author.name' => ['required', 'string', 'max:100'],
                'main_author.lastname' => ['required', 'string', 'max:150'],
                'main_author_country_id' => [
                    'required',
                    'integer',
                    'exists:countries,id',
                ],
                'presentation_type' => 'required',
                'abstract_type' => 'required',
                'title' => 'required|max:250',
                'body' => 'required|max:3000',
            ]);

            // VALIDACIÓN DE CO-AUTHORS
            $coAuthorsNames = $request->co_authors_name ?? [];
            $coAuthorsLastnames = $request->co_authors_lastname ?? [];
            $hasCoAuthors = false;
            foreach ($coAuthorsNames as $i => $name) {
                $lastname = $coAuthorsLastnames[$i] ?? '';
                if ($name || $lastname) {
                    $hasCoAuthors = true;
                    break;
                }
            }

            if (!$hasCoAuthors) {
                return back()->withErrors([
                    'co_authors' => 'At least one co-author is required.'
                ])->withInput();
            }

            // VALIDACIÓN DE INSTITUTIONS
            $institutionsNames = $request->institutions ?? [];
            $hasInstitutions = false;
            foreach ($institutionsNames as $inst) {
                if ($inst) {
                    $hasInstitutions = true;
                    break;
                }
            }

            if (!$hasInstitutions) {
                return back()->withErrors([
                    'institutions' => 'At least one institution is required.'
                ])->withInput();
            }

            // VALIDACIÓN DE KEYWORDS
            if (!is_array($keywords) || count($keywords) < 1 || count($keywords) > 3) {
                return back()->withErrors([
                    'keywords' => 'Select between 1 and 3 keywords'
                ])->withInput();
            }
        }

        // =========================
        // MAIN AUTHOR
        // =========================
        $mainAuthor = [
            'name' => trim(
                $request->input('main_author.name', '')
            ),

            'lastname' => trim(
                $request->input('main_author.lastname', '')
            ),
        ];

        // =========================
        // 🔥 CO-AUTHORS
        // =========================
        $coAuthors = [];
        foreach ($request->co_authors_name as $i => $name) {
            $lastname = $request->co_authors_lastname[$i] ?? '';
            if (!$name && !$lastname) continue;

            $idCo = $request->co_authors_id[$i] ?? 'ca_' . $i;

            $coAuthors[] = [
                'id' => $idCo,
                'name' => $name,
                'lastname' => $lastname,
            ];
        }

        // =========================
        // 🔥 INSTITUTIONS
        // =========================
        $institutions = [];
        foreach ($request->institutions as $i => $instName) {
            if (!$instName) continue;

            $coauthorsIds = json_decode($request->institution_coauthors[$i] ?? '[]', true);

            $institutions[] = [
                'name' => $instName,
                'coauthors' => $coauthorsIds
            ];
        } 

        // =========================
        // 💾 UPDATE
        // =========================
        $abstractPost->presentation_type = $request->presentation_type;
        $abstractPost->abstract_type = $request->abstract_type;
        $abstractPost->subtopic = $request->subtopic;
        $abstractPost->title = $request->title;
        $abstractPost->body = $request->body;

        $abstractPost->status = $action;

        $abstractPost->main_author = $mainAuthor;
        $abstractPost->main_author_country_id = $request->main_author_country_id;

        $abstractPost->co_authors = $coAuthors;
        $abstractPost->institutions = $institutions;
        $abstractPost->keywords = $keywords;

        $abstractPost->save();

        // =========================
        // 🔁 REDIRECCIÓN
        // =========================
        if ($action === 'draft') {
            return redirect()->route('abstract_posts.edit', $abstractPost->id)
                ->with('success', 'Draft updated');
        }

        return redirect()->route('abstract_posts.show', $abstractPost->id)
            ->with('success', 'Updated and sent successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(AbstractPost $abstractPost)
    {
        // 🔒 Validar que sea el dueño
        if ($abstractPost->user_id != \Auth::id()) {
            abort(403);
        }

        $abstractPost->delete();

        return redirect()->route('abstract_posts.index')
            ->with('success', 'Deleted successfully');
    }

    public function updateStatus(Request $request, AbstractPost $abstractPost)
    {
        //Validar que sea el administrador
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'comment' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,submitted,accepted,rejected',
        ]);

        $oldStatus = $abstractPost->status;

        $abstractPost->status = $request->status;
        $abstractPost->save();

        //Registrar Nota
        $abstractPostNote = new AbstractPostNote();
        $abstractPostNote->abstract_post_id = $abstractPost->id;
        $abstractPostNote->user_id = auth()->id();
        $abstractPostNote->comment = $request->comment;
        $abstractPostNote->status_change = "Changed status from {$oldStatus} to {$request->status}";


        $abstractPostNote->save();

        return redirect()->route('abstract_posts.show', $abstractPost->id)
            ->with('success', 'Status changed successfully');
    }

    public function exportExcelAbstracts(){

        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Unauthorized action.');
        }

        $filename = 'Abstracts_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new AbstractPostExport(),
            $filename
        );
    }


    public function pdf(AbstractPost $abstractPost)
    {

        // 🔒 Validar que sea el dueño o Admistrador
        if ($abstractPost->user_id != \Auth::id() && !\Auth::user()->hasRole('Administrador')) {
            abort(403);
        }



        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Abstract N° ' . $abstractPost->id);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);


        $coAuthorsData = $abstractPost->co_authors ?? [];
        $institutionsData = $abstractPost->institutions ?? [];

        // Compatibilidad con registros antiguos
        if (is_string($coAuthorsData)) {
            $coAuthorsData = json_decode($coAuthorsData, true) ?? [];
        }

        if (is_string($institutionsData)) {
            $institutionsData = json_decode($institutionsData, true) ?? [];
        }

        $coAuthors = collect(
            is_array($coAuthorsData) ? $coAuthorsData : []
        );

        // Numerar las instituciones
        $institutions = collect(
            is_array($institutionsData) ? $institutionsData : []
        )->values()->map(function ($institution, $index) {
            $institution['number'] = $index + 1;

            return $institution;
        });

        // Instituciones asociadas al autor principal
        $mainAuthorInstitutions = [];

        foreach ($institutions as $institution) {
            $institutionAuthors = $institution['coauthors'] ?? [];

            if (
                is_array($institutionAuthors) &&
                in_array('main_author', $institutionAuthors, true)
            ) {
                $mainAuthorInstitutions[] = $institution['number'];
            }
        }

        // Instituciones asociadas a cada coautor
        $coAuthorsMapped = $coAuthors->map(function ($coauthor) use ($institutions) {
            $institutionNumbers = [];

            foreach ($institutions as $institution) {
                $institutionAuthors = $institution['coauthors'] ?? [];

                if (
                    isset($coauthor['id']) &&
                    is_array($institutionAuthors) &&
                    in_array($coauthor['id'], $institutionAuthors, true)
                ) {
                    $institutionNumbers[] = $institution['number'];
                }
            }

            $coauthor['institutions'] = $institutionNumbers;

            return $coauthor;
        });


        $mainAuthorInstitutions = '';
        if(!empty($mainAuthorInstitutions)){
            $mainAuthorInstitutions = implode(', ', $mainAuthorInstitutions);
        }

        $htmlcoautinstitutio = '';
        if ($coAuthorsMapped->isNotEmpty()) {
            $htmlcoautinstitutio .= '<span class="text-black mb-4">';
            foreach ($coAuthorsMapped as $index => $coauthor) {
                $htmlcoautinstitutio .= '<span>';
                $htmlcoautinstitutio .= ($coauthor['name'] ?? '') . ' ' . ($coauthor['lastname'] ?? '');
                if (!empty($coauthor['institutions'])) {
                    $htmlcoautinstitutio .= '<sup><b>' . implode(',', $coauthor['institutions']) . '</b></sup>';
                }
                $htmlcoautinstitutio .= '</span>';
                if ($index < ($coAuthorsMapped->count() - 1)) {
                    $htmlcoautinstitutio .= '<br>';
                }
            }
            $htmlcoautinstitutio .= '</span>';
        } else {
            $htmlcoautinstitutio .= '<span class="text-muted">No co-authors registered.</span>';
        }
        if ($institutions->isNotEmpty()) {
            $htmlcoautinstitutio .= '<br><br>';
            $htmlcoautinstitutio .= '<span class="text-black fst-italic">';
            foreach ($institutions as $index => $institution) {
                $htmlcoautinstitutio .= '<span>';
                $htmlcoautinstitutio .= '<sup><b>'.$institution['number'].'</b></sup>';
                $htmlcoautinstitutio .= ($institution['name'] ?? '');
                $htmlcoautinstitutio .= '</span>';
                if ($index < ($institutions->count() - 1)) {
                    $htmlcoautinstitutio .= '&nbsp;&nbsp;';
                }
            }
            $htmlcoautinstitutio .= '</span>';
        } else {
            $htmlcoautinstitutio .= '<span class="text-muted">No institutions registered.</span>';
        }

        $keywords = $abstractPost->keywords ?? [];
        // Compatibilidad con registros antiguos
        if (is_string($keywords)) {
            $keywords = json_decode($keywords, true) ?? [];
        }

        $htmlkeywords = '';
        if (is_array($keywords) && count($keywords)) {

            foreach ($keywords as $index => $keyword) {

                $htmlkeywords .= '<span class="tag">' . $keyword . '</span>';

                if ($index < (count($keywords) - 1)) {
                    $htmlkeywords .= ', ';
                }
            }

        } else {

            $htmlkeywords .= '<span class="text-muted">No keywords registered.</span>';
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
                margin-bottom: 0px;         }
        </style>

        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><span class="title">Abstract N°: ' . e($abstractPost->id) . '</span></td>
                <td align="right" style="font-size: 8px;">
                    <span class="label" style="color: #000000;">Country:</span><span class="value" style="color: #000000;"> ' . e($abstractPost->mainAuthorCountry->name) . '</span><br>
                    <span class="label" style="color: #a7a7a7;">Created:</span><span class="value" style="color: #a7a7a7;"> ' . e($abstractPost->created_at) . '</span><br>
                    <span class="label" style="color: #a7a7a7;">Updated:</span><span class="value" style="color: #a7a7a7;"> ' . e($abstractPost->updated_at) . '</span>
                </td>
            </tr>
        </table>
        <br>

        <div class="item"><span class="label">' . e($abstractPost->presentation_type) . '</span></div>

        <div class="item"><span class="label">Abstract Type:</span><br><span class="value">' . e($abstractPost->abstract_type) . '</span></div>

        <div class="item"><span class="label">Sub theme:</span><br><span class="value">' . e($abstractPost->subtopic) . '</span></div>

        <div class="item"><span class="label">Title:</span><br><span class="value">' . e($abstractPost->title) . '</span></div>

        <div class="item"><span class="label">Main author:</span><br><span class="value">' . e($abstractPost->main_author["name"] ?? "") . ' ' . e($abstractPost->main_author["lastname"] ?? "") . '</span><sup><b>'.$mainAuthorInstitutions.'</b></sup></div>

        <div class="item"><span class="label">Co-authors:</span><br><span class="value">' . $htmlcoautinstitutio . '</span></div>

        <div class="item"><span class="label">Body text:</span><br><span class="value">' . nl2br(e($abstractPost->body)) . '</span></div>

        <div class="item"><span class="label">Keywords:</span><br><span class="value">' . $htmlkeywords . '</span></div>

        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('workshop-' . $abstractPost->id . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    private function participantAbstractLimitReached(int $userId): bool
    {
        return auth()->user()->hasRole('Participante')
            && AbstractPost::where('user_id', $userId)->count() >= self::MAX_ABSTRACTS_PER_PARTICIPANT;
    }

    private function abstractLimitMessage(): string
    {
        return 'You have reached the maximum limit of 3 abstracts per participant.';
    }


}
