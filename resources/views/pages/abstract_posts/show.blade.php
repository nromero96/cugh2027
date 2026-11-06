@extends('layouts.app')

@section('content')

<style>
@media print {

    /* Ocultar botones */
    .no-print {
        display: none !important;
    }

    /* Opcional: quitar márgenes raros */
    body {
        margin: 0;
    }

    /* Opcional: ajustar contenido */
    .layout-px-spacing {
        padding: 0 !important;
    }

    .main-content{
        margin-top: 0 !important;
    }

}
</style>

<style>
#abstract-evaluation {
    border: 1px solid #e7eaf3;
    border-radius: 14px;
    overflow: hidden;
}

#abstract-evaluation .evaluation-heading {
    background: linear-gradient(135deg, #f4f7ff 0%, #eef8ff 100%);
    border-bottom: 1px solid #e3e8f2;
}

#abstract-status-card { border: 1px solid #e6e8ec; border-radius: 14px; background: #fff; overflow: hidden; box-shadow: 0 4px 18px rgba(26, 35, 51, .06); }
#abstract-status-card .abstract-status-heading { display: flex; align-items: center; gap: 11px; padding: 17px 20px; border-bottom: 1px solid #eef0f3; background: #fff7f8; }
#abstract-status-card .abstract-status-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; flex: 0 0 auto; border-radius: 11px; background: #f7d2d6; color: #a71929; }
#abstract-status-card .abstract-status-title { margin: 0; color: #27303b; font-size: 16px; font-weight: 700; }
#abstract-status-card .abstract-status-subtitle { display: block; margin-top: 2px; color: #687384; font-size: 12px; }
#abstract-status-card .abstract-current-status { margin-left: auto; padding: 6px 10px; border: 1px solid #efc2c8; border-radius: 999px; background: #fff; color: #a71929; font-size: 12px; font-weight: 700; white-space: nowrap; }
#abstract-status-card .abstract-status-form { padding: 18px 20px 20px; }
#abstract-status-card .abstract-status-label { margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 600; }
#abstract-status-card .form-control, #abstract-status-card .form-select { min-height: 42px; border-color: #d8dfe6; border-radius: 9px; background-color: #fff; color: #344054; }
#abstract-status-card .form-control:focus, #abstract-status-card .form-select:focus { border-color: #cc1f2f; box-shadow: 0 0 0 3px rgba(204, 31, 47, .12); }
#abstract-status-card .abstract-status-save { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; border: 1px solid #cc1f2f; border-radius: 9px; background: #cc1f2f; color: #fff; font-weight: 600; white-space: nowrap; }
#abstract-status-card .abstract-status-save:hover { border-color: #ab1828; background: #ab1828; color: #fff; box-shadow: 0 4px 12px rgba(204, 31, 47, .2); }
#abstract-status-card .abstract-status-save:focus-visible { outline: 3px solid rgba(204, 31, 47, .25); outline-offset: 2px; }
#abstract-status-card .abstract-status-history { padding: 17px 20px 19px; border-top: 1px solid #eef0f3; }
#abstract-status-card .abstract-history-title { display: flex; align-items: center; gap: 8px; margin: 0 0 12px; color: #344054; font-size: 13px; font-weight: 700; }
#abstract-status-card .abstract-history-count { padding: 3px 8px; border-radius: 999px; background: #f2f4f7; color: #667085; font-size: 11px; }
#abstract-status-card .abstract-history-list { margin: 0; padding: 0; list-style: none; }
#abstract-status-card .abstract-history-item { padding: 11px 13px; border: 1px solid #e9edf1; border-radius: 9px; background: #fafbfc; }
#abstract-status-card .abstract-history-item + .abstract-history-item { margin-top: 9px; }
#abstract-status-card .abstract-history-comment { margin: 0; color: #344054; font-size: 13px; line-height: 1.45; white-space: pre-wrap; overflow-wrap: anywhere; }
#abstract-status-card .abstract-history-meta { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 6px; color: #687384; font-size: 12px; }
#abstract-status-card .abstract-history-empty { margin: 0; color: #687384; font-size: 13px; }
@media (max-width: 575.98px) { #abstract-status-card .abstract-status-heading { flex-wrap: wrap; } #abstract-status-card .abstract-current-status { margin-left: 51px; } }

.score-legend-item {
    height: 100%;
    padding: 12px 14px;
    border: 1px solid transparent;
    border-radius: 10px;
}

.score-legend-item strong,
.score-legend-item span {
    display: block;
}

.score-legend-item span {
    margin-top: 2px;
    font-size: 1.05rem;
    font-weight: 700;
}

.score-legend-poor { background: #fff5f5; border-color: #ffd6d6; color: #b42318; }
.score-legend-fair { background: #fff9eb; border-color: #ffe4a8; color: #946200; }
.score-legend-good { background: #eef8ff; border-color: #cce9ff; color: #1261a0; }
.score-legend-excellent { background: #effaf3; border-color: #cdeed8; color: #16733c; }

.evaluation-criterion {
    padding: 16px;
    background: #fff;
    border: 1px solid #e5e9f2;
    border-radius: 12px;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.evaluation-criterion:focus-within {
    border-color: #6f8cff;
    box-shadow: 0 0 0 3px rgba(111, 140, 255, .12);
}

.criterion-number {
    display: inline-flex;
    flex: 0 0 34px;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    margin-right: 12px;
    color: #fff;
    background: #4361ee;
    border-radius: 50%;
    font-weight: 700;
}

.criterion-score-input {
    max-width: 135px;
    margin-left: auto;
}

.criterion-score-input .form-control {
    min-height: 46px;
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
}

.evaluation-summary {
    padding: 14px 16px;
    background: #f7f9fc;
    border: 1px solid #e4e9f1;
    border-radius: 12px;
}

@media (max-width: 767.98px) {
    .criterion-score-input {
        max-width: none;
        margin-top: 12px;
    }
}
</style>

<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6">
                                <h4 class="display-inline-block">
                                    Abstract N°: {{ $abstract_post->id }} 
                                    @if ($abstract_post->status == 'draft')
                                        <span class="badge bg-light-warning mt-2">Draft</span>
                                    @elseif ($abstract_post->status == 'submitted')
                                        <span class="badge bg-light-info mt-2">Submitted</span>
                                    @elseif ($abstract_post->status == 'qualified')
                                        <span class="badge bg-light-primary mt-2">Qualified</span>
                                    @elseif ($abstract_post->status == 'accepted')
                                        <span class="badge bg-light-success mt-2">Accepted</span>
                                    @elseif ($abstract_post->status == 'rejected')
                                        <span class="badge bg-light-danger mt-2">Rejected</span>
                                    @elseif ($abstract_post->status == 'not_accepted')
                                        <span class="badge bg-light-danger mt-2">Not Accepted</span>
                                    @endif

                                    @if(\Auth::user()->hasRole('Participante') && $abstract_post->status == 'draft')
                                        <a href="{{ route('abstract_posts.edit', $abstract_post->id) }}" class="btn btn-outline-info btn-link px-2 py-0">Edit Abstract</a>
                                    @endif

                                </h4>

                                

                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6 text-end">
                                <span class="badge bg-light-secondary mt-2">Last Update: {{ $abstract_post->updated_at }}</span>

                                @if($abstract_post->mainAuthorCountry)
                                    <span class="d-block mt-2">
                                        {{ $abstract_post->mainAuthorCountry->name }}
                                    </span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <p class="text-black">{{ $abstract_post->presentation_type }}</p>
                                </div>

                                <div class="col-md-12">
                                    <label for="optionsAbstractType" class="form-label text-muted mb-0 d-block">Abstract Type:</label>
                                    <p class="text-black">{{ $abstract_post->abstract_type }}</p>
                                </div>

                                <div class="col-md-12">
                                    <label for="selectSubtopic" class="form-label text-muted mb-0 d-block">Sub theme:</label>
                                    <p class="text-black">{{ $abstract_post->subtopic }}</p>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0 d-block">Title:</label>
                                    <p class="text-black fw-bold">{{ $abstract_post->title }}</p>
                                </div>

                                

                                @php
                                    $coAuthorsData = $abstract_post->co_authors ?? [];
                                    $institutionsData = $abstract_post->institutions ?? [];

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
                                @endphp

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0 d-block">
                                        Main author:
                                    </label>

                                    <p class="text-black mb-0">
                                        {{ $abstract_post->main_author['name'] ?? '' }}
                                        {{ $abstract_post->main_author['lastname'] ?? '' }}

                                        @if(!empty($mainAuthorInstitutions))
                                            <sup><b>{{ implode(',', $mainAuthorInstitutions) }}</b></sup>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0 d-block">
                                        Co-authors:
                                    </label>

                                    @if($coAuthorsMapped->isNotEmpty())
                                        <p class="text-black mb-4">
                                            @foreach($coAuthorsMapped as $coauthor)
                                                <span>
                                                    {{ $coauthor['name'] ?? '' }}
                                                    {{ $coauthor['lastname'] ?? ''}}
                                                    @if(!empty($coauthor['institutions']))
                                                    <sup><b>{{ implode(',', $coauthor['institutions']) }}</b></sup>
                                                    @endif
                                                </span>

                                                @if(!$loop->last)
                                                    <br>
                                                @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p class="text-muted">
                                            No co-authors registered.
                                        </p>
                                    @endif

                                    @if($institutions->isNotEmpty())
                                        <p class="text-black fst-italic">
                                            @foreach($institutions as $institution)
                                                <span>
                                                    <sup><b>{{ $institution['number'] }}</b></sup>{{ $institution['name'] ?? '' }}
                                                </span>

                                                @if(!$loop->last)
                                                    &nbsp;
                                                @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p class="text-muted">
                                            No institutions registered.
                                        </p>
                                    @endif
                                </div>

                                

                                <div class="col-md-12">
                                    <label for="inputDescription" class="form-label text-muted d-block mb-0">
                                        Body text:
                                    </label>
                                    <p class="text-black">
                                        {!! nl2br(e($abstract_post->body)) !!}
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-2 d-block">
                                        Keywords:
                                    </label>

                                    @php
                                        $keywords = $abstract_post->keywords ?? [];

                                        // Compatibilidad con registros antiguos
                                        if (is_string($keywords)) {
                                            $keywords = json_decode($keywords, true) ?? [];
                                        }
                                    @endphp

                                    <p class="text-black">
                                        @if(is_array($keywords) && count($keywords))
                                            @foreach($keywords as $keyword)
                                                <span class="tag">{{ $keyword }}</span>

                                                @if(!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">No keywords registered.</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-12 text-end no-print">
                                    @if(\Auth::user()->hasRole('Participante') && $abstract_post->status == 'draft')
                                        <a href="{{ route('abstract_posts.edit', $abstract_post->id) }}" class="btn btn-info">Edit Abstract</a>
                                    @endif

                                    <a href="{{ request('from') === 'assigned' && $reviewAssignment ? route('abstract_posts.assigned') : route('abstract_posts.index') }}" class="btn btn-outline-secondary">Back</a>
                                    <a href="{{ route('abstract_posts.pdf', $abstract_post->id)}}" class="btn btn-outline-primary" target="_blank">Download PDF</a>
                                    

                                </div>
                            </div>
                    </div>
                </div>

                @if(\Auth::user()->hasRole('Administrador') && in_array($abstract_post->status, ['qualified', 'submitted']))
                <div class="statbox widget box box-shadow mt-3 no-print">
                    <div class="widget-header py-3 px-3">
                        <h5 class="mb-1">Accept Abstract</h5>
                        <small class="text-muted">Choose the presentation type. Saving will set the status to Accepted and record the change in the internal history.</small>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form action="{{ route('abstract_posts.accept', $abstract_post->id) }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-12 col-md-5">
                                <label for="admin-presentation-type" class="form-label">Presentation Type</label>
                                <select id="admin-presentation-type" name="presentation_type" class="form-select @error('presentation_type') is-invalid @enderror" required>
                                    <option value="Oral Presentation" {{ old('presentation_type', $abstract_post->presentation_type) === 'Oral Presentation' ? 'selected' : '' }}>Oral Presentation</option>
                                    <option value="Poster" {{ old('presentation_type', $abstract_post->presentation_type) === 'Poster' ? 'selected' : '' }}>Poster</option>
                                </select>
                                @error('presentation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Status on save</label>
                                <div class="form-control bg-light">Accepted</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Save Acceptance</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                <div class="statbox widget box box-shadow mt-3" id="abstract-status-card">
                    <div class="abstract-status-heading">
                        <span class="abstract-status-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16M4 10h16M4 15h10"/><path d="m15 18 2 2 4-5"/></svg>
                        </span>
                        <div>
                            <h6 class="abstract-status-title">{{ \Auth::user()->hasRole('Administrador') && !in_array($abstract_post->status, ['qualified', 'accepted', 'rejected', 'not_accepted'], true) ? 'Add comment and status' : 'Internal comments' }}</h6>
                            <span class="abstract-status-subtitle">Visible to the conference team only</span>
                        </div>
                        <span class="abstract-current-status">{{ ucwords(str_replace('_', ' ', $abstract_post->status)) }}</span>
                    </div>
                    @if(\Auth::user()->hasRole('Administrador'))
                        <form class="abstract-status-form" action="{{ route('abstract_posts.updatestatus', $abstract_post->id) }}" method="POST">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-6">
                                    <label class="form-label abstract-status-label" for="abstract-status-comment">Comment</label>
                                    <input type="text" id="abstract-status-comment" class="form-control @error('comment') is-invalid @enderror" placeholder="Add an internal comment" name="comment" value="{{ old('comment') }}" maxlength="1000" required>
                                    @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label abstract-status-label" for="abstract-new-status">New status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="abstract-new-status" name="status" required>
                                        <option value="draft" {{ old('status', $abstract_post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="submitted" {{ old('status', $abstract_post->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="qualified" {{ old('status', $abstract_post->status) == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                        <option value="accepted" {{ old('status', $abstract_post->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ old('status', $abstract_post->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="not_accepted" {{ old('status', $abstract_post->status) == 'not_accepted' ? 'selected' : '' }}>Not accepted</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="submit" class="btn abstract-status-save w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                        Save update
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                    <div class="abstract-status-history">
                        <h6 class="abstract-history-title">Comment history <span class="abstract-history-count">{{ $abstract_post->notes->count() }}</span></h6>
                        <ul class="abstract-history-list">
                            @forelse($abstract_post->notes as $note)
                                <li class="abstract-history-item">
                                    <p class="abstract-history-comment">{{ $note->comment ?: 'No comment' }}</p>
                                    <div class="abstract-history-meta">
                                        <span>{{ $note->status_change }}</span>
                                        <time datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('d/m/Y h:i A') }}</time>
                                    </div>
                                </li>
                            @empty
                                <li class="abstract-history-empty">No comments have been added yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @endif

                @if(\Auth::user()->hasRole('Administrador'))
                    <div class="statbox widget box box-shadow mt-3 no-print">
                        <div class="widget-header py-3 px-3"><h5 class="mb-0">Email History</h5></div>
                        <div class="widget-content widget-content-area pt-0 table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead><tr><th>Type</th><th>Recipient</th><th>Sent At</th><th>Attachment</th><th>Copy</th></tr></thead>
                                <tbody>
                                    @forelse($abstract_post->emails as $email)
                                        <tr>
                                            <td>{{ ucfirst($email->kind) }}</td>
                                            <td>{{ $email->recipient_email }}</td>
                                            <td>{{ $email->sent_at ? $email->sent_at->format('Y-m-d H:i:s') : ucfirst($email->status) }}</td>
                                            <td>
                                                @if($email->attachment_path)
                                                    <a href="{{ route('abstract_posts.emails.attachment', $email->id) }}" style="color:#CC1F2F;text-decoration:underline;text-underline-offset:2px;">{{ $email->attachment_filename }}</a>
                                                @else
                                                    None
                                                @endif
                                            </td>
                                            <td>@if($email->status === 'sent')<a href="{{ route('abstract_posts.emails.show', $email->id) }}" target="_blank" rel="noopener" style="color:#CC1F2F;text-decoration:underline;text-underline-offset:2px;">View sent email</a>@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">No emails have been sent for this abstract.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(\Auth::user()->hasRole('Administrador'))
                    @php
                        $completedReviews = $abstract_post->reviewers->filter(function ($reviewer) {
                            return $reviewer->pivot->average_score !== null;
                        });
                        $reviewsComplete = $abstract_post->reviewers->isNotEmpty()
                            && $completedReviews->count() === $abstract_post->reviewers->count();
                        $completedCount = $completedReviews->count();
                        $combinedScore = $completedReviews->sum(function ($reviewer) {
                            return (int) $reviewer->pivot->score_1 + (int) $reviewer->pivot->score_2
                                + (int) $reviewer->pivot->score_3 + (int) $reviewer->pivot->score_4
                                + (int) $reviewer->pivot->score_5;
                        });
                        $overallAverage = $completedCount ? $combinedScore / ($completedCount * 5) : null;
                        // Truncate only the display to two decimals; keep the exact score calculation intact.
                        $averageHundredths = $completedCount ? intdiv($combinedScore * 100, $completedCount * 5) : null;
                        $totalHundredths = $completedCount ? intdiv($combinedScore * 100, $completedCount) : null;
                    @endphp
                    <div class="statbox widget box box-shadow mt-3 no-print">
                        <div class="widget-header py-3 px-3">
                            <h5 class="mb-1">Review Results</h5>
                            <small class="text-muted">Evaluation scores and notes are visible to administrators only.</small>
                        </div>
                        <div class="widget-content widget-content-area pt-0">
                            @if($abstract_post->reviewers->isEmpty())
                                <div class="alert alert-info mb-0">No reviewers have been assigned to this abstract.</div>
                            @else
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <span class="badge {{ $reviewsComplete ? 'badge-light-success' : 'badge-light-warning' }}">
                                        {{ $reviewsComplete ? 'Complete' : 'Pending' }}: {{ $completedReviews->count() }} / {{ $abstract_post->reviewers->count() }} evaluations
                                    </span>
                                    @if($overallAverage !== null)
                                        <strong>{{ $reviewsComplete ? 'Overall average' : 'Partial average' }}: {{ sprintf('%d.%02d', intdiv($averageHundredths, 100), $averageHundredths % 100) }} / 10</strong>
                                        <span class="text-muted">Equivalent total: {{ sprintf('%d.%02d', intdiv($totalHundredths, 100), $totalHundredths % 100) }} / 50</span>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Reviewer</th>
                                                <th class="text-center">Structure</th>
                                                <th class="text-center">Clarity</th>
                                                <th class="text-center">Innovation</th>
                                                <th class="text-center">Challenge</th>
                                                <th class="text-center">Impact</th>
                                                <th class="text-center">Total / 50</th>
                                                <th class="text-center">Average / 10</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($abstract_post->reviewers as $reviewer)
                                                @php
                                                    $scores = collect(range(1, 5))->map(function ($number) use ($reviewer) {
                                                        return $reviewer->pivot->{'score_'.$number};
                                                    });
                                                    $hasResult = $reviewer->pivot->average_score !== null;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ trim($reviewer->name.' '.$reviewer->lastname.' '.$reviewer->second_lastname) }}</strong>
                                                        <small class="d-block text-muted">{{ $reviewer->email }}</small>
                                                    </td>
                                                    @foreach($scores as $score)
                                                        <td class="text-center">{{ $hasResult ? $score : '—' }}</td>
                                                    @endforeach
                                                    <td class="text-center">{{ $hasResult ? $scores->sum() : '—' }}</td>
                                                    <td class="text-center">{{ $hasResult ? number_format((float) $reviewer->pivot->average_score, 2) : 'Pending' }}</td>
                                                    <td style="min-width: 180px; white-space: pre-wrap;">{{ $hasResult ? ($reviewer->pivot->reviewer_note ?: '—') : '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($reviewAssignment)
                    <div class="statbox widget box box-shadow mt-3 no-print" id="abstract-evaluation">
                        <div class="widget-header evaluation-heading py-3 px-3">
                            <h5 class="mb-1">Abstract Evaluation</h5>
                            <small class="text-muted mb-0"><strong>Abstract Scoring Criteria:</strong> Each criterion can be scored 0–10 (highest). Total maximum score: 50.</small>
                        </div>
                        <div class="widget-content widget-content-area pt-0">
                            @php
                                $evaluationLocked = in_array($abstract_post->status, ['qualified', 'accepted', 'rejected', 'not_accepted'], true) || $reviewAssignment->pivot->average_score !== null;
                                $scoringCriteria = [
                                    1 => 'Structure of the abstract',
                                    2 => 'Clarity of the writing',
                                    3 => 'Degree of innovation of the program/initiative',
                                    4 => 'Does the abstract address an important global health challenge (human, environmental, social, political, etc.)?',
                                    5 => 'The degree to which solutions/recommendations proposed could impact policy or a global health challenge',
                                ];
                            @endphp

                            @if($evaluationLocked)
                                <div class="alert alert-info" role="status">
                                    {{ $reviewAssignment->pivot->average_score !== null
                                        ? 'Your evaluation has been submitted and can no longer be changed.'
                                        : 'Evaluation is closed for this abstract.' }}
                                </div>
                            @endif

                            <div class="mb-4">
                                <div class="d-flex flex-wrap align-items-baseline gap-2 mb-2">
                                    <h6 class="mb-0">Scoring Guide</h6>
                                    <small class="text-muted">Use these ranges as a reference.</small>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6 col-lg-3">
                                        <div class="score-legend-item score-legend-poor"><strong>Poor</strong><span>1–3</span></div>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <div class="score-legend-item score-legend-fair"><strong>Fair</strong><span>4–6</span></div>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <div class="score-legend-item score-legend-good"><strong>Good</strong><span>7–8</span></div>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <div class="score-legend-item score-legend-excellent"><strong>Excellent</strong><span>9–10</span></div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('abstract_posts.review', $abstract_post) }}" id="abstract-review-form">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    @for($criterion = 1; $criterion <= 5; $criterion++)
                                        @php $scoreField = 'score_'.$criterion; @endphp
                                        <div class="col-12">
                                            <div class="evaluation-criterion">
                                                <div class="row align-items-center g-0">
                                                    <div class="col-md">
                                                        <label for="{{ $scoreField }}" class="d-flex align-items-start mb-0">
                                                            <span class="criterion-number">{{ $criterion }}</span>
                                                            <span>
                                                                <span class="d-block fw-semibold text-dark">{{ $scoringCriteria[$criterion] }}</span>
                                                                <small class="text-muted">Maximum score: 10</small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-auto criterion-score-input">
                                                        <div class="input-group">
                                                            <input
                                                                type="number"
                                                                id="{{ $scoreField }}"
                                                                name="{{ $scoreField }}"
                                                                class="form-control review-score @error($scoreField) is-invalid @enderror"
                                                                min="0"
                                                                max="10"
                                                                step="1"
                                                                inputmode="numeric"
                                                                aria-label="Score for criterion {{ $criterion }}"
                                                                value="{{ old($scoreField, $reviewAssignment->pivot->{$scoreField}) }}"
                                                                {{ $evaluationLocked ? 'readonly' : 'required' }}
                                                            >
                                                            <span class="input-group-text">/ 10</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor

                                    <div class="col-12">
                                        <div class="evaluation-summary">
                                            <div class="row align-items-center g-3">
                                                <div class="col">
                                                    <span class="text-muted d-block">Evaluation summary</span>
                                                    <strong>Maximum total score: 50</strong>
                                                </div>
                                                <div class="col-6 col-md-2 text-md-center">
                                                    <small class="text-muted d-block">Total</small>
                                                    <strong class="fs-5" id="review-total">—</strong>
                                                </div>
                                                <div class="col-6 col-md-2 text-md-center">
                                                    <small class="text-muted d-block">Average</small>
                                                    <strong class="fs-5" id="review-average">
                                                        {{ $reviewAssignment->pivot->average_score !== null ? number_format($reviewAssignment->pivot->average_score, 2) : '—' }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="reviewer_note" class="form-label">Review note <span class="text-muted">(optional)</span></label>
                                        <textarea
                                            id="reviewer_note"
                                            name="reviewer_note"
                                            class="form-control @error('reviewer_note') is-invalid @enderror"
                                            rows="4"
                                            maxlength="5000"
                                            placeholder="Add any comments about your evaluation"
                                            {{ $evaluationLocked ? 'readonly' : '' }}
                                        >{{ old('reviewer_note', $reviewAssignment->pivot->reviewer_note) }}</textarea>
                                    </div>

                                    @unless($evaluationLocked)
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary">Save Evaluation</button>
                                        </div>
                                    @endunless
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>

<script>
function printSection(className) {
    const content = document.querySelector('.' + className).innerHTML;

    const printWindow = window.open('', '', 'width=800,height=600');

    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .no-print { display: none !important; }
                sup { font-size: 0.7em; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

document.addEventListener('DOMContentLoaded', function () {
    const reviewForm = document.getElementById('abstract-review-form');
    const scoreInputs = Array.from(document.querySelectorAll('#abstract-review-form .review-score'));
    const average = document.getElementById('review-average');
    const total = document.getElementById('review-total');

    if (!scoreInputs.length || !average || !total) {
        return;
    }

    function updateAverage() {
        const scores = scoreInputs.map(function (input) {
            return input.value.trim() === '' ? NaN : Number(input.value);
        }).filter(function (score) {
            return Number.isInteger(score) && score >= 0 && score <= 10;
        });

        if (scores.length !== 5) {
            total.textContent = '—';
            average.textContent = '—';
            return;
        }

        const totalScore = scores.reduce(function (sum, score) { return sum + score; }, 0);
        total.textContent = totalScore + ' / 50';
        const averageHundredths = totalScore * 20;
        average.textContent = Math.trunc(averageHundredths / 100) + '.' + String(averageHundredths % 100).padStart(2, '0');
    }

    scoreInputs.forEach(function (input) {
        input.addEventListener('input', updateAverage);
    });

    reviewForm.addEventListener('submit', function (event) {
        if (!window.confirm('Submit this evaluation? Once submitted, you will not be able to change your scores or review note.')) {
            event.preventDefault();
        }
    });

    updateAverage();
});
</script>


@endsection
