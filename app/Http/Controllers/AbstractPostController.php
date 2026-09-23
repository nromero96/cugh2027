<?php

namespace App\Http\Controllers;

use App\Models\AbstractPost;
use App\Models\AbstractPostNote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\ReviewerCandidate;
use App\Http\Requests\AssignAbstractReviewersRequest;
use App\Http\Requests\SubmitAbstractReviewRequest;
use App\Services\AbstractReviewerAssignmentImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use DomainException;

use App\Exports\AbstractPostExport;
use Maatwebsite\Excel\Facades\Excel;

use TCPDF;

class AbstractPostController extends Controller
{
    private const MAX_ABSTRACTS_PER_PARTICIPANT = 3;
    private const SUBMISSIONS_CLOSED_MESSAGE = 'ABSTRACT SUBMISSION IS NOW CLOSED';

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
        $isAdministrator = auth()->user()->hasRole('Administrador');
        $isStaff = $isAdministrator || auth()->user()->hasRole('Secretaria');
        $rejectedPage = $request->attributes->get('rejected_listing', false);

        if ($rejectedPage) {
            $status = 'rejected';
        }

        $query = AbstractPost::with('user');
        if ($isAdministrator && !$rejectedPage) {
            $query->with('reviewers:id,name,lastname,second_lastname,email');
        }

        if ($rejectedPage && $isStaff) {
            $query->where('status', 'rejected');
        } elseif ($isAdministrator) {
            // Administrators manage all active abstracts from the main listing.
            $query->where('status', '!=', 'rejected');
        } else {
            $query->where('user_id', $userid);
            if ($rejectedPage) {
                $query->where('status', 'rejected');
            }
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
                        $escapedTerm = addcslashes(mb_strtolower($term, 'UTF-8'), '%_\\');
                        $like = "%{$escapedTerm}%";

                        $allTermsQuery->where(function ($fieldQuery) use ($like) {
                            $fieldQuery
                                ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(abstract_posts.main_author, '$.name'))) LIKE ?", [$like])
                                ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(abstract_posts.main_author, '$.lastname'))) LIKE ?", [$like])
                                ->orWhereRaw('LOWER(abstract_posts.title) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(abstract_posts.presentation_type) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(abstract_posts.abstract_type) LIKE ?', [$like])
                                ->orWhereHas('user', function ($userQuery) use ($like) {
                                    $userQuery
                                        ->whereRaw('LOWER(email) LIKE ?', [$like])
                                        ->orWhereRaw('LOWER(name) LIKE ?', [$like])
                                        ->orWhereRaw('LOWER(lastname) LIKE ?', [$like])
                                        ->orWhereRaw('LOWER(second_lastname) LIKE ?', [$like]);
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

        $allowedStatuses = $isAdministrator
            ? ['draft', 'submitted', 'qualified', 'accepted']
            : ['draft', 'submitted', 'qualified', 'accepted', 'rejected'];

        if (!$rejectedPage && in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        $abstract_posts = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $abstractCount = AbstractPost::where('user_id', $userid)->count();
        $abstractLimitReached = auth()->user()->hasRole('Participante')
            && $abstractCount >= self::MAX_ABSTRACTS_PER_PARTICIPANT;

        $abstractReport = null;
        if ($isStaff) {
            $abstractReport = AbstractPost::query()
                ->when(!$isAdministrator, function ($reportQuery) use ($userid) {
                    $reportQuery->where('user_id', $userid);
                })
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
                ->selectRaw("SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted")
                ->selectRaw("SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified")
                ->selectRaw("SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted")
                ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
                ->first();
        }

        return view('pages.abstract_posts.index')
            ->with($data)
            ->with('abstract_posts', $abstract_posts)
            ->with('search', $search)
            ->with('status', $status)
            ->with('rejectedPage', $rejectedPage)
            ->with('abstractCount', $abstractCount)
            ->with('abstractLimitReached', $abstractLimitReached)
            ->with('abstractReport', $abstractReport)
            ->with('maxAbstracts', self::MAX_ABSTRACTS_PER_PARTICIPANT);

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.submissions.closed', [
            'message' => self::SUBMISSIONS_CLOSED_MESSAGE,
        ]);

        /* Submission form retained for a future reopening.
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
        */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->view('pages.submissions.closed', [
            'message' => self::SUBMISSIONS_CLOSED_MESSAGE,
        ], 410);

        /* Submission storage retained for a future reopening.
        $id_user = \Auth::user()->id;

        $request->validate([
            'action' => ['required', 'in:draft,submitted'],
            'co_authors_name' => ['nullable', 'array', 'max:20'],
            'co_authors_name.*' => ['nullable', 'string', 'max:100'],
            'co_authors_lastname' => ['nullable', 'array', 'max:20'],
            'co_authors_lastname.*' => ['nullable', 'string', 'max:150'],
            'co_authors_id' => ['nullable', 'array', 'max:20'],
        ], [
            'action.required' => 'Please choose whether to save the abstract as a draft or send it for review.',
            'action.in' => 'The selected abstract action is invalid. Please try again.',
            'co_authors_name.max' => 'A maximum of 20 co-authors is allowed.',
            'co_authors_lastname.max' => 'A maximum of 20 co-authors is allowed.',
            'co_authors_id.max' => 'A maximum of 20 co-authors is allowed.',
        ]);

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

        try {
            $abstractWasCreated = DB::transaction(function () use ($abstractpost, $id_user) {
                // Lock the participant row so simultaneous requests cannot exceed the limit.
                User::whereKey($id_user)->lockForUpdate()->first();

                if ($this->participantAbstractLimitReached($id_user)) {
                    return false;
                }

                $abstractpost->save();

                return true;
            });
        } catch (\Throwable $exception) {
            \Log::error('Abstract creation failed.', [
                'user_id' => $id_user,
                'action' => $action,
                'exception' => $exception,
            ]);

            return back()->withInput()->withErrors([
                'submission' => 'We could not save your abstract. Please try again. If the problem continues, contact support.',
            ]);
        }

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
        */
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function show(AbstractPost $abstractPost)
    {
        if (!$this->canViewAbstract($abstractPost)) {
            abort(403);
        }

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($abstractPost->user_id);


        // 🔥 cargar relación
        $abstractPost->load(['user', 'mainAuthorCountry', 'notes.user', 'reviewers']);
        $reviewAssignment = $abstractPost->reviewers->firstWhere('id', auth()->id());
        if (request('from') === 'assigned' && $reviewAssignment) {
            $data['category_name'] = 'assigned_abstracts';
        }

        return view('pages.abstract_posts.show')
            ->with($data)
            ->with('abstract_post', $abstractPost)
            ->with('user', $user)
            ->with('reviewAssignment', $reviewAssignment);
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

        $request->validate([
            'action' => ['required', 'in:draft,submitted'],
        ], [
            'action.required' => 'Please choose whether to save the abstract as a draft or send it for review.',
            'action.in' => 'The selected abstract action is invalid. Please try again.',
        ]);

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

        try {
            $abstractPost->save();
        } catch (\Throwable $exception) {
            \Log::error('Abstract update failed.', [
                'user_id' => \Auth::id(),
                'abstract_post_id' => $abstractPost->id,
                'action' => $action,
                'exception' => $exception,
            ]);

            return back()->withInput()->withErrors([
                'submission' => 'We could not save your abstract. Please try again. If the problem continues, contact support.',
            ]);
        }

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
        // // 🔒 Validar que sea el dueño
        // if ($abstractPost->user_id != \Auth::id()) {
        //     abort(403);
        // }

        // //verificar que solo este en draft
        // if ($abstractPost->status !== 'draft') {
        //     return redirect()->route('abstract_posts.index')
        //         ->with('error', 'Only draft abstracts can be deleted.');
        // }

        // $abstractPost->delete();

        // return redirect()->route('abstract_posts.index')
        //     ->with('success', 'Deleted successfully');
    }

    public function updateStatus(Request $request, AbstractPost $abstractPost)
    {
        //Validar que sea el administrador
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'comment' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,submitted,qualified,accepted,rejected',
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

    public function reviewerAssignments(Request $request)
    {
        $this->ensureAdministrator();

        $search = trim((string) $request->input('search', ''));
        $abstracts = AbstractPost::query()
            ->with(['user', 'reviewers:id,name,lastname,second_lastname,email'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $id = ltrim($search, '#');
                    if (ctype_digit($id)) {
                        $searchQuery->orWhere('abstract_posts.id', (int) $id);
                    }

                    $searchQuery->orWhere('title', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $reviewers = User::query()
            ->whereIn('email', ReviewerCandidate::query()->select('email'))
            ->withCount('assignedAbstracts')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get(['id', 'name', 'lastname', 'second_lastname', 'email']);

        return view('pages.abstract_posts.reviewer-assignments', [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'abstracts' => $abstracts,
            'reviewers' => $reviewers,
            'search' => $search,
        ]);
    }

    public function importReviewerAssignments(Request $request, AbstractReviewerAssignmentImportService $importer)
    {
        $this->ensureAdministrator();
        $request->validate([
            'assignment_file' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls,csv'],
        ], [
            'assignment_file.required' => 'Select an Excel or CSV file to import.',
            'assignment_file.mimes' => 'The file must be XLSX, XLS or CSV.',
            'assignment_file.max' => 'The file may not be larger than 10 MB.',
        ]);

        try {
            $result = $importer->import($request->file('assignment_file'));
        } catch (DomainException $exception) {
            return back()->withErrors(['assignment_file' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['assignment_file' => 'The file could not be imported. Verify that it is a valid, readable spreadsheet.']);
        }

        $previous = $request->session()->pull('abstract_assignment_import_report');
        if (is_array($previous) && !empty($previous['path'])) {
            Storage::disk('local')->delete($previous['path']);
        }

        if ($result['rejected_rows']) {
            $token = (string) Str::uuid();
            $path = 'abstract-assignment-import-errors/'.$token.'.csv';
            Storage::disk('local')->put($path, $importer->rejectedRowsCsv($result['rejected_rows']));
            $request->session()->put('abstract_assignment_import_report', [
                'token' => $token,
                'path' => $path,
                'count' => count($result['rejected_rows']),
            ]);
        }

        return redirect()->route('abstract_posts.assignments')->with('success',
            $result['added'].' reviewer assignments added, '.$result['unchanged'].' rows unchanged, and '
            .count($result['rejected_rows']).' rows rejected.'
        );
    }

    public function downloadReviewerAssignmentErrors(Request $request, $token)
    {
        $this->ensureAdministrator();
        $report = $request->session()->get('abstract_assignment_import_report');
        abort_unless(
            is_array($report)
            && hash_equals((string) ($report['token'] ?? ''), (string) $token)
            && Storage::disk('local')->exists($report['path'] ?? ''),
            404
        );

        $request->session()->forget('abstract_assignment_import_report');
        return response()->download(
            Storage::disk('local')->path($report['path']),
            'abstract-assignment-import-errors-'.now()->format('Y-m-d-His').'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        )->deleteFileAfterSend(true);
    }

    public function assignedAbstracts()
    {
        $user = auth()->user();
        abort_unless($user && $user->assignedAbstracts()->exists(), 403);

        $abstracts = $user->assignedAbstracts()
            ->with('user')
            ->orderByDesc('abstract_posts.id')
            ->paginate(20);

        return view('pages.abstract_posts.assigned', [
            'category_name' => 'assigned_abstracts',
            'page_name' => 'assigned_abstracts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'abstracts' => $abstracts,
        ]);
    }

    public function updateReviewerAssignments(AssignAbstractReviewersRequest $request, AbstractPost $abstractPost)
    {
        $reviewerIds = collect($request->validated()['reviewer_ids'] ?? [])
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($abstractPost, $reviewerIds) {
            $lockedAbstract = AbstractPost::whereKey($abstractPost->id)->lockForUpdate()->firstOrFail();
            $hasSubmittedReviews = DB::table('abstract_post_reviewers')
                ->where('abstract_post_id', $abstractPost->id)
                ->whereNotNull('average_score')
                ->exists();

            if ($lockedAbstract->status === 'qualified' || $hasSubmittedReviews) {
                throw ValidationException::withMessages([
                    'reviewer_ids' => 'Reviewer assignments cannot be changed after an evaluation has been submitted.',
                ]);
            }

            $abstractPost->reviewers()->sync($reviewerIds);
        });

        return back()->with('success', 'Reviewers assigned successfully.');
    }

    public function submitReview(SubmitAbstractReviewRequest $request, AbstractPost $abstractPost)
    {
        $validated = $request->validated();
        $scores = collect(range(1, 5))->map(function ($number) use ($validated) {
            return (int) $validated['score_'.$number];
        });

        DB::transaction(function () use ($abstractPost, $validated, $scores) {
            $lockedAbstract = AbstractPost::whereKey($abstractPost->id)->lockForUpdate()->firstOrFail();
            $assignment = DB::table('abstract_post_reviewers')
                ->where('abstract_post_id', $abstractPost->id)
                ->where('reviewer_id', auth()->id())
                ->lockForUpdate()
                ->first();

            abort_unless($assignment, 403);

            if ($lockedAbstract->status === 'qualified' || $assignment->average_score !== null) {
                throw ValidationException::withMessages([
                    'review' => 'This evaluation has already been submitted and cannot be changed.',
                ]);
            }

            DB::table('abstract_post_reviewers')
                ->where('id', $assignment->id)
                ->update([
                    'score_1' => $scores[0],
                    'score_2' => $scores[1],
                    'score_3' => $scores[2],
                    'score_4' => $scores[3],
                    'score_5' => $scores[4],
                    'average_score' => $scores->sum() / 5,
                    'reviewer_note' => $validated['reviewer_note'] ?? null,
                    'updated_at' => now(),
                ]);

            $hasPendingReviews = DB::table('abstract_post_reviewers')
                ->where('abstract_post_id', $abstractPost->id)
                ->whereNull('average_score')
                ->exists();

            if (!$hasPendingReviews) {
                $lockedAbstract->status = 'qualified';
                $lockedAbstract->save();
            }
        });

        return back()->with('success', 'Your evaluation was submitted successfully and can no longer be changed.');
    }


    public function pdf(AbstractPost $abstractPost)
    {

        if (!$this->canViewAbstract($abstractPost)) {
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

    public function rejected(Request $request)
    {
        if (
            !auth()->user()->hasRole('Administrador') &&
            !auth()->user()->hasRole('Secretaria')
        ) {
            abort(403);
        }

        $request->attributes->set('rejected_listing', true);

        return $this->index($request);
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

    private function ensureAdministrator(): void
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
    }

    private function canViewAbstract(AbstractPost $abstractPost): bool
    {
        if ($abstractPost->user_id === auth()->id()) {
            return true;
        }

        if (auth()->user()->hasRole(['Administrador', 'Secretaria'])) {
            return true;
        }

        return $abstractPost->reviewers()->where('users.id', auth()->id())->exists();
    }


}
