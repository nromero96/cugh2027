@extends('layouts.app')

@section('content')
@php $canSendReviewInstructions = Auth::user()->hasRole('Administrador'); @endphp
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
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
                        <strong>The import could not be completed.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('import_errors') && count(session('import_errors')))
                    <div class="alert alert-warning" role="alert">
                        <strong>Rows requiring review:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('reviewer_import_report'))
                    <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2" role="alert">
                        <span>
                            <strong>{{ number_format(session('reviewer_import_report.count')) }} rows were not imported.</strong>
                            Download the file, correct the indicated errors and import it again.
                        </span>
                        <a href="{{ route('reviewer_candidates.import_errors', session('reviewer_import_report.token')) }}" class="btn btn-info btn-sm">
                            Download rejected rows
                        </a>
                    </div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="statbox widget box box-shadow h-100 p-3">
                            <span class="text-muted d-block">Reviewers</span>
                            <strong class="fs-3">{{ number_format($totalReviewers) }}</strong>
                        </div>
                    </div>
                </div>

                {{-- <div class="statbox widget box box-shadow mb-3">
                    <div class="widget-header pt-3 px-3">
                        <h4 class="px-0 mb-1">Import reviewer list</h4>
                        <small class="text-muted mb-3">Upload an XLSX, XLS or CSV file. Existing records are updated by email, so importing the same file will not create duplicates.</small>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form action="{{ route('reviewer_candidates.import') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-center">
                            @csrf
                            <div class="col-lg-8">
                                <label for="reviewer_file" class="form-label">Reviewer spreadsheet</label>
                                <input type="file" name="reviewer_file" id="reviewer_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Maximum size: 10 MB and 10,000 data rows.</small>
                            </div>
                            <div class="col-lg-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">Import reviewers</button>
                                <a href="{{ route('reviewer_candidates.template') }}" class="btn btn-outline-secondary">Template</a>
                            </div>
                        </form>
                    </div>
                </div> --}}

                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-3 px-3">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <h4 class="px-0 mb-3">Reviewer Directory</h4>
                            </div>
                            <div class="col-md-7">
                                <form method="GET" action="{{ route('reviewer_candidates.index') }}" class="d-flex flex-wrap gap-2 mb-3">
                                    <input type="search" name="search" class="form-control flex-grow-1" style="width: 180px;" value="{{ $search }}" placeholder="Name, email, institution or country">
                                    <select name="notification" class="form-select" style="width: 145px;" aria-label="Notification status">
                                        <option value="">All emails</option>
                                        <option value="pending" {{ $notification === 'pending' ? 'selected' : '' }}>Not sent</option>
                                        <option value="sent" {{ $notification === 'sent' ? 'selected' : '' }}>Sent</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </button>
                                    @if($search !== '' || in_array($notification, ['sent', 'pending'], true))
                                        <a href="{{ route('reviewer_candidates.index') }}" class="btn btn-outline-secondary">Clear</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        @if($canSendReviewInstructions)
                            <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                <form id="review-instructions-form" method="POST" action="{{ route('reviewer_candidates.review_instructions.send') }}">
                                    @csrf
                                    <button type="submit" id="send-review-instructions" class="btn btn-primary btn-sm" disabled>Send instructions (<span id="selected-reviewer-count">0</span>)</button>
                                </form>
                                <label class="d-inline-flex align-items-center gap-2 mb-0">
                                    <input type="checkbox" id="select-page-reviewers" class="form-check-input mt-0">
                                    <span>Select all unsent on this page</span>
                                </label>
                                <a href="{{ route('reviewer_candidates.review_instructions.preview') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">Preview email</a>
                            </div>
                            <p class="text-muted small mb-3">Only selected reviewers on this page will receive the email. Already-sent messages cannot be sent again from here.</p>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Reviewer</th>
                                        <th>Affiliation</th>
                                        <th>Expertise</th>
                                        <th>Account</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reviewers as $reviewer)
                                        <tr>
                                            <td style="min-width: 190px;">
                                                <div class="d-flex align-items-start gap-2">
                                                    @if($canSendReviewInstructions && !$reviewer->review_instructions_sent_at)
                                                        <input type="checkbox" name="reviewer_ids[]" value="{{ $reviewer->id }}" form="review-instructions-form" class="form-check-input reviewer-notification-checkbox mt-1" aria-label="Select {{ $reviewer->email }} for review instructions">
                                                    @endif
                                                    <strong>{{ trim($reviewer->salutation.' '.$reviewer->first_name.' '.$reviewer->last_name) ?: '—' }}</strong>
                                                </div>
                                                <a class="small text-break" href="mailto:{{ $reviewer->email }}">{{ $reviewer->email }}</a>
                                            </td>
                                            <td style="min-width: 200px;">
                                                @if($reviewer->professional_position_academic_title)
                                                    <span class="d-block">{{ $reviewer->professional_position_academic_title }}</span>
                                                @endif
                                                <span class="d-block text-muted small">{{ $reviewer->institution ?: 'No institution' }}</span>
                                                @if($reviewer->country)
                                                    <span class="d-block text-muted small">{{ $reviewer->country }}</span>
                                                @endif
                                            </td>
                                            <td style="min-width: 145px;">
                                                <details>
                                                    <summary class="text-primary" style="cursor: pointer;">View expertise</summary>
                                                    <div class="small mt-2" style="max-width: 320px;">
                                                        <strong class="d-block">Review interest</strong>
                                                        <span class="d-block text-muted text-break mb-2">{{ $reviewer->review_interest ?: '—' }}</span>
                                                        <strong class="d-block">Panel languages</strong>
                                                        <span class="d-block text-muted text-break mb-2">{{ $reviewer->panel_languages ?: '—' }}</span>
                                                        <strong class="d-block">Subthemes</strong>
                                                        <span class="d-block text-muted text-break">{{ $reviewer->subthemes ?: '—' }}</span>
                                                    </div>
                                                </details>
                                            </td>
                                            <td class="text-center" style="min-width: 130px;">
                                                @if($reviewer->registeredUser)
                                                    <span class="badge badge-light-success">Registered</span>
                                                @else
                                                    <span class="badge badge-light-secondary d-inline-block mb-2">Not registered</span>
                                                    <form method="POST" action="{{ route('reviewer_candidates.create_user', $reviewer) }}" onsubmit="return confirm('Create a participant account and email the login credentials to {{ addslashes($reviewer->email) }}?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm text-nowrap">Create user</button>
                                                    </form>
                                                @endif
                                                @if($reviewer->review_instructions_sent_at)
                                                    <span class="badge badge-light-success d-block mt-2">Instructions sent</span>
                                                    <small class="text-muted">{{ $reviewer->review_instructions_sent_at->format('Y-m-d H:i') }}</small>
                                                @else
                                                    <span class="badge badge-light-warning d-block mt-2">Instructions not sent</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                {{ $search !== '' ? 'No reviewers matched your search.' : 'No reviewers have been imported yet.' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $reviewers->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('review-instructions-form');
    if (!form) return;

    const checkboxes = Array.from(document.querySelectorAll('.reviewer-notification-checkbox'));
    const selectAll = document.getElementById('select-page-reviewers');
    const button = document.getElementById('send-review-instructions');
    const count = document.getElementById('selected-reviewer-count');

    function updateSelection() {
        const selected = checkboxes.filter(function (checkbox) { return checkbox.checked; }).length;
        count.textContent = selected;
        button.disabled = selected === 0;
        selectAll.checked = checkboxes.length > 0 && selected === checkboxes.length;
        selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) { checkbox.checked = selectAll.checked; });
        updateSelection();
    });
    checkboxes.forEach(function (checkbox) { checkbox.addEventListener('change', updateSelection); });
    form.addEventListener('submit', function (event) {
        if (!window.confirm('Send abstract review instructions to ' + count.textContent + ' selected reviewer(s)? This action sends real emails.')) {
            event.preventDefault();
        }
    });
});
</script>
@endsection
