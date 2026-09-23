@extends('layouts.app')

@section('content')
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

                @if(session('abstract_assignment_import_report'))
                    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="alert">
                        <span><strong>{{ number_format(session('abstract_assignment_import_report.count')) }} rows were not imported.</strong> Download the errors, correct those rows and import them again.</span>
                        <a href="{{ route('abstract_posts.assignments.import_errors', session('abstract_assignment_import_report.token')) }}" class="btn btn-warning btn-sm">Download rejected rows</a>
                    </div>
                @endif

                <div class="statbox widget box box-shadow mb-3">
                    <div class="widget-header pt-3 px-3">
                        <h4 class="px-0 mb-1">Import Reviewer Assignments</h4>
                        <p class="text-muted mb-2">Upload an XLSX, XLS or CSV file with abstract_post_id, Revisor 1, Revisor 2 and Revisor 3. Email matching ignores case. Existing assignments and submitted evaluations are preserved.</p>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form method="POST" action="{{ route('abstract_posts.assignments.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-9">
                                <label for="assignment_file" class="form-label">Assignment spreadsheet</label>
                                <input type="file" id="assignment_file" name="assignment_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Maximum size: 10 MB and 10,000 data rows.</small>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Import Assignments</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-3 px-3">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <h4 class="px-0">Assign Abstract Reviewers</h4>
                                <p class="text-muted mb-3">Select up to 3 reviewers for each abstract.</p>
                            </div>
                            <div class="col-md-7">
                                <form method="GET" action="{{ route('abstract_posts.assignments') }}" class="d-flex gap-2 mb-3">
                                    <input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Abstract ID, title or participant e-mail">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    @if($search !== '')
                                        <a href="{{ route('abstract_posts.assignments') }}" class="btn btn-outline-secondary">Clear</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        @if($reviewers->isEmpty())
                            <div class="alert alert-warning" role="alert">
                                No registered users match an email address in Reviewer Candidates.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Abstract</th>
                                        <th style="min-width: 340px;">Reviewers</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($abstracts as $abstract)
                                        @php
                                            $assignedReviewerIds = $abstract->reviewers->pluck('id')->all();
                                            $assignmentsLocked = $abstract->status === 'qualified' || $abstract->reviewers->contains(function ($reviewer) {
                                                return $reviewer->pivot->average_score !== null;
                                            });
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('abstract_posts.show', $abstract->id) }}" class="text-primary">#{{ $abstract->id }}</a>
                                            </td>
                                            <td>
                                                <strong class="d-block">{{ $abstract->title ?: 'Untitled abstract' }}</strong>
                                                <small class="text-muted d-block">
                                                    {{ trim(($abstract->main_author['name'] ?? '').' '.($abstract->main_author['lastname'] ?? '')) }}
                                                    @if($abstract->user) — {{ $abstract->user->email }} @endif
                                                </small>
                                                <span class="badge badge-light-secondary text-capitalize mt-1">{{ $abstract->status === 'draft' ? 'In progress' : $abstract->status }}</span>
                                            </td>
                                            <td>
                                                <form id="reviewer-form-{{ $abstract->id }}" method="POST" action="{{ route('abstract_posts.assignments.update', $abstract) }}" class="reviewer-assignment-form" data-locked="{{ $assignmentsLocked ? '1' : '0' }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-1">
                                                        @foreach($reviewers as $reviewer)
                                                            <div class="col-lg-6">
                                                                <label class="border rounded p-2 w-100 d-flex align-items-start gap-2 reviewer-option">
                                                                    <input type="checkbox" name="reviewer_ids[]" value="{{ $reviewer->id }}" class="form-check-input mt-1 reviewer-checkbox" {{ in_array($reviewer->id, $assignedReviewerIds, true) ? 'checked' : '' }} {{ $assignmentsLocked ? 'disabled' : '' }}>
                                                                    <span>
                                                                        <span class="d-block">{{ trim($reviewer->name.' '.$reviewer->lastname.' '.$reviewer->second_lastname) }}</span>
                                                                        <small class="text-muted d-block">{{ $reviewer->email }}</small>
                                                                        <small class="text-info">{{ $reviewer->assigned_abstracts_count }} assigned</small>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <small class="text-muted selection-count">{{ count($assignedReviewerIds) }} / 3 selected</small>
                                                    @if($assignmentsLocked)
                                                        <small class="d-block text-muted">Assignments are locked after an evaluation is submitted.</small>
                                                    @endif
                                                </form>
                                            </td>
                                            <td>
                                                <button type="submit" form="reviewer-form-{{ $abstract->id }}" class="btn btn-primary btn-sm" {{ $reviewers->isEmpty() || $assignmentsLocked ? 'disabled' : '' }}>Save</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No abstracts were found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $abstracts->onEachSide(1)->links() }}
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
    document.querySelectorAll('.reviewer-assignment-form').forEach(function (form) {
        if (form.dataset.locked === '1') {
            return;
        }

        const checkboxes = Array.from(form.querySelectorAll('.reviewer-checkbox'));
        const count = form.querySelector('.selection-count');

        function refresh() {
            const selected = checkboxes.filter(function (checkbox) { return checkbox.checked; }).length;
            count.textContent = selected + ' / 3 selected';
            count.classList.toggle('text-danger', selected >= 3);
            checkboxes.forEach(function (checkbox) {
                checkbox.disabled = selected >= 3 && !checkbox.checked;
            });
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', refresh);
        });
        refresh();
    });
});
</script>
@endsection
