<?php

namespace App\Http\Controllers;

use App\Models\AbstractPost;
use App\Models\AbstractPostNote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;

use App\Exports\AbstractPostExport;
use Maatwebsite\Excel\Facades\Excel;

use TCPDF;

class AbstractPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userid = auth()->id();

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria')){
            $abstract_posts = AbstractPost::with('user')
            ->where('status', '!=', 'rechazado')
            ->orderBy('id', 'desc')
            ->get();
        }else{
            $abstract_posts = AbstractPost::with('user')
            ->where('user_id', $userid)
            ->where('status', '!=', 'rechazado')
            ->orderBy('id', 'desc')
            ->get();
        }

        
        return view('pages.abstract_posts.index')->with($data)->with('abstract_posts', $abstract_posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = \Auth::user()->id;

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

        $abstractpost->save();

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

}
