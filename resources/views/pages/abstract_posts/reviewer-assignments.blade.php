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

                {{-- <div class="statbox widget box box-shadow mb-3">
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
                </div> --}}

                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-3 px-3">
                        <div class="row align-items-center">
                            <div class="col-lg-12 mb-1">
                                <h4 class="px-0 pb-0">Assign Abstract Reviewers</h4>
                                <small class="text-muted mb-3">Find a reviewer's assignments, then replace them where evaluations are still pending.</small>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <form method="GET" action="{{ route('abstract_posts.assignments') }}" class="row g-2 align-items-end mb-3">
                                    <div class="col-md-4">
                                        <label for="assignment-search" class="form-label mb-1">Abstract</label>
                                        <input type="search" id="assignment-search" name="search" class="form-control" value="{{ $search }}" placeholder="ID, title or participant e-mail">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="assignment-reviewer" class="form-label mb-1">Assigned reviewer</label>
                                        <select id="assignment-reviewer" name="reviewer_id" class="form-select">
                                            <option value="">All reviewers</option>
                                            @foreach($reviewers as $reviewer)
                                                <option value="{{ $reviewer->id }}" {{ $assignedReviewerId === $reviewer->id ? 'selected' : '' }}>{{ trim($reviewer->name.' '.$reviewer->lastname) }} — {{ $reviewer->email }} ({{ $reviewer->assigned_abstracts_count }} assigned)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="assignment-state" class="form-label mb-1">Assignments</label>
                                        <select id="assignment-state" name="assignment_state" class="form-select">
                                            <option value="">All</option>
                                            <option value="editable" {{ $assignmentState === 'editable' ? 'selected' : '' }}>Can be changed</option>
                                            <option value="locked" {{ $assignmentState === 'locked' ? 'selected' : '' }}>Locked</option>
                                            <option value="unassigned" {{ $assignmentState === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2">Apply filters</button>
                                        @if($search !== '' || $assignedReviewerId > 0 || $assignmentState !== '')
                                        <a href="{{ route('abstract_posts.assignments') }}" class="btn btn-outline-secondary">Clear</a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        <small class="text-muted mb-2">{{ number_format($abstracts->total()) }} abstracts found. Evaluated reviewers are locked; pending slots can be changed individually.</small>
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
                                        <th style="min-width: 190px;">Reviewer 1</th>
                                        <th style="min-width: 190px;">Reviewer 2</th>
                                        <th style="min-width: 190px;">Reviewer 3</th>
                                        <th style="width: 290px; max-width: 290px;">Abstract</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($abstracts as $abstract)
                                        @php
                                            $assignedReviewerIds = $abstract->reviewers->pluck('id')->all();
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('abstract_posts.show', $abstract->id) }}" class="text-primary">#{{ $abstract->id }}</a>
                                            </td>
                                            @for($slot = 1; $slot <= 3; $slot++)
                                                @php
                                                    $assigned = $abstract->reviewers->values()->get($slot - 1);
                                                    $evaluated = $assigned && ($assigned->pivot->average_score !== null || collect(range(1, 5))->contains(function ($number) use ($assigned) {
                                                        return $assigned->pivot->{'score_'.$number} !== null;
                                                    }));
                                                    $slotLocked = $abstract->status === 'qualified' || $evaluated;
                                                    $canAssign = $slot <= count($assignedReviewerIds) + 1;
                                                @endphp
                                                <td>
                                                    @if($assigned)
                                                        <span class="d-block fw-semibold">{{ trim($assigned->name.' '.$assigned->lastname.' '.$assigned->second_lastname) }}</span>
                                                        <small class="text-muted d-block text-break">{{ $assigned->email }}</small>
                                                    @else
                                                        <span class="text-muted d-block">Not assigned</span>
                                                    @endif
                                                    @if($slotLocked)
                                                        <span class="badge badge-light-success mt-2">{{ $evaluated ? 'Evaluated · Locked' : 'Locked' }}</span>
                                                    @elseif($canAssign)
                                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#changeReviewerModal"
                                                            data-action="{{ route('abstract_posts.assignments.slot.update', [$abstract, $slot]) }}"
                                                            data-abstract="{{ $abstract->id }}" data-slot="{{ $slot }}"
                                                            data-current-id="{{ $assigned ? $assigned->id : '' }}"
                                                            data-assigned-ids="{{ implode(',', $assignedReviewerIds) }}"
                                                            {{ $reviewers->isEmpty() ? 'disabled' : '' }}>{{ $assigned ? 'Change' : 'Assign' }}</button>
                                                    @endif
                                                </td>
                                            @endfor
                                            <td style="max-width: 290px;">
                                                <strong class="d-block text-truncate" title="{{ $abstract->title ?: 'Untitled abstract' }}">{{ \Illuminate\Support\Str::limit($abstract->title ?: 'Untitled abstract', 48, '...') }}</strong>
                                                <small class="text-muted d-block text-truncate">
                                                    {{ trim(($abstract->main_author['name'] ?? '').' '.($abstract->main_author['lastname'] ?? '')) }}
                                                    @if($abstract->user) — {{ $abstract->user->email }} @endif
                                                </small>
                                                <span class="badge badge-light-secondary text-capitalize mt-1">{{ $abstract->status === 'draft' ? 'In progress' : $abstract->status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No abstracts were found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="modal fade" id="changeReviewerModal" tabindex="-1" aria-labelledby="changeReviewerTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST" id="changeReviewerForm" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="current_reviewer_id" id="currentReviewerId">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="changeReviewerTitle">Change reviewer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="reviewerPickerSearch" class="form-label">Find a reviewer</label>
                                        <input type="search" id="reviewerPickerSearch" class="form-control mb-2" placeholder="Search name or e-mail">
                                        <label for="reviewerPicker" class="form-label">New reviewer</label>
                                        <select id="reviewerPicker" name="reviewer_id" class="form-select" size="7" required>
                                            @foreach($reviewers as $reviewer)
                                                <option value="{{ $reviewer->id }}">{{ trim($reviewer->name.' '.$reviewer->lastname.' '.$reviewer->second_lastname) }} — {{ $reviewer->email }} ({{ $reviewer->assigned_abstracts_count }} assigned)</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-2">The other reviewers and completed evaluations will stay unchanged.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save change</button>
                                    </div>
                                </form>
                            </div>
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
    const modal = document.getElementById('changeReviewerModal');
    const form = document.getElementById('changeReviewerForm');
    const select = document.getElementById('reviewerPicker');
    const search = document.getElementById('reviewerPickerSearch');
    const candidates = Array.from(select.options).map(function (option) {
        return { value: option.value, label: option.textContent };
    });
    let assignedIds = [];

    function showCandidates() {
        const term = search.value.trim().toLocaleLowerCase();
        const previousValue = select.value;
        select.replaceChildren();
        candidates.forEach(function (candidate) {
            if (assignedIds.includes(candidate.value) || !candidate.label.toLocaleLowerCase().includes(term)) {
                return;
            }
            select.add(new Option(candidate.label, candidate.value));
        });
        select.value = previousValue;
        if (!select.value) {
            select.selectedIndex = -1;
        }
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        form.action = button.dataset.action;
        document.getElementById('currentReviewerId').value = button.dataset.currentId;
        document.getElementById('changeReviewerTitle').textContent = (button.dataset.currentId ? 'Change' : 'Assign') + ' Reviewer ' + button.dataset.slot + ' · Abstract #' + button.dataset.abstract;
        assignedIds = button.dataset.assignedIds.split(',').filter(Boolean);
        search.value = '';
        showCandidates();
        select.selectedIndex = -1;
    });
    search.addEventListener('input', showCandidates);
    form.addEventListener('submit', function (event) {
        if (!select.value) {
            event.preventDefault();
            select.focus();
        }
    });
});
</script>
@endsection
