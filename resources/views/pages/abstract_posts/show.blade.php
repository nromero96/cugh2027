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
                                    @elseif ($abstract_post->status == 'rejected')
                                        <span class="badge bg-light-danger mt-2">Rejected</span>
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

                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                <div class="statbox widget box box-shadow mt-3">
                    <div class="widget-header py-2 px-3">
                            <div class="row">
                                <div class="col-12">
                                    <h6>Add comment and status</h6>
                                </div>
                            </div>

                            @if($abstract_post->status != 'qualified')
                            <form class="row" action="{{ route('abstract_posts.updatestatus', $abstract_post->id) }}" method="POST">
                                @csrf
                                <div class="col-7">
                                    <label class="form-label text-muted mb-0 d-block">Comment:</label>
                                    <input type="text" class="form-control" placeholder="Comment" name="comment" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label text-muted mb-0 d-block">Status from <b>{{ $abstract_post->status }}</b> to: </label>
                                    <select class="form-select" name="status">
                                        <option value="draft" {{ $abstract_post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="submitted" {{ $abstract_post->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="qualified" {{ $abstract_post->status == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                        <option value="accepted" {{ $abstract_post->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ $abstract_post->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-2 pt-2">
                                    <button class="btn btn-secondary w-100 mt-3">Add</button>
                                </div>
                            </form>

                            @endif

                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                        <h6 class="fw-bold text-primary">Comments (for internal use only)</h6>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        @foreach($abstract_post->notes as $note)
                        <p class="text-black mb-0">{{ $note->comment }}</p>
                        <small class="text-muted d-block"> {{ $note->status_change }}</small>
                        <small class="text-muted"> {{ $note->created_at->format('d/m/Y h:i A') }}</small>
                        <hr class="my-1">
                        @endforeach
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
                        $overallAverage = $completedReviews->isNotEmpty()
                            ? $completedReviews->avg(function ($reviewer) {
                                return (float) $reviewer->pivot->average_score;
                            })
                            : null;
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
                                        <strong>{{ $reviewsComplete ? 'Overall average' : 'Partial average' }}: {{ number_format($overallAverage, 2) }} / 10</strong>
                                        <span class="text-muted">Equivalent total: {{ number_format($overallAverage * 5, 2) }} / 50</span>
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
                                $evaluationLocked = $abstract_post->status === 'qualified' || $reviewAssignment->pivot->average_score !== null;
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
        average.textContent = (totalScore / 5).toFixed(2);
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
