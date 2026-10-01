@extends('layouts.app')

@section('content')
<div class="layout-px-spacing"><div class="middle-content container-xxl p-0"><div class="row layout-spacing"><div class="col-12 layout-top-spacing">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger"><strong>Please correct the following errors:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if(session('panel_assignment_import_report'))
        <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="alert">
            <span><strong>{{ number_format(session('panel_assignment_import_report.count')) }} rows were not imported.</strong> Download the errors, correct those rows and import them again.</span>
            <a href="{{ route('panels.assignments.import_errors', session('panel_assignment_import_report.token')) }}" class="btn btn-warning btn-sm">Download rejected rows</a>
        </div>
    @endif
    <div class="statbox widget box box-shadow mb-3">
        <div class="widget-header pt-3 px-3"><h4 class="px-0 mb-1">Import Panel Reviewer Assignments</h4><p class="text-muted mb-2">Upload an XLSX, XLS or CSV file with panel_id, Revisor 1 and Revisor 2. Email matching ignores case. Existing assignments and submitted evaluations are preserved.</p></div>
        <div class="widget-content widget-content-area pt-0">
            <form method="POST" action="{{ route('panels.assignments.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-9"><label for="assignment_file" class="form-label">Assignment spreadsheet</label><input type="file" id="assignment_file" name="assignment_file" class="form-control" accept=".xlsx,.xls,.csv" required><small class="text-muted">Maximum size: 10 MB and 10,000 data rows.</small></div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Import Assignments</button></div>
            </form>
        </div>
    </div>
    <div class="statbox widget box box-shadow">
        <div class="widget-header pt-3 px-3"><div class="row align-items-center">
            <div class="col-md-6"><h4 class="px-0 mb-1">Assign Panel Reviewers</h4><p class="text-muted mb-3">Select up to 2 reviewers for each panel.</p></div>
            <div class="col-md-6"><form method="GET" action="{{ route('panels.assignments') }}" class="d-flex gap-2 mb-3"><input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Panel ID, title or contact e-mail"><button class="btn btn-primary" type="submit">Search</button>@if($search !== '')<a href="{{ route('panels.assignments') }}" class="btn btn-outline-secondary">Clear</a>@endif</form></div>
        </div></div>
        <div class="widget-content widget-content-area pt-0">
            @if($reviewers->isEmpty())<div class="alert alert-warning">No registered users match an email address in Reviewer Directory.</div>@endif
            <div class="table-responsive"><table class="table table-hover table-bordered align-middle mb-0"><thead><tr><th>ID</th><th>Panel</th><th style="min-width:340px">Reviewers</th><th>Action</th></tr></thead><tbody>
                @forelse($panels as $panel)
                    @php
                        $assignedIds = $panel->reviewers->pluck('id')->all();
                        $locked = in_array($panel->status, ['Qualified', 'Rejected'], true) || $panel->reviewers->contains(function ($reviewer) { return $reviewer->pivot->average_score !== null; });
                    @endphp
                    <tr>
                        <td><a href="{{ route('panels.show', $panel) }}">#{{ $panel->id }}</a></td>
                        <td><strong class="d-block">{{ $panel->title ?: 'Untitled panel' }}</strong><small class="text-muted">{{ $panel->contact_email }}</small><span class="badge badge-light-secondary d-block mt-1">{{ $panel->status }}</span></td>
                        <td><form id="panel-reviewers-{{ $panel->id }}" class="panel-reviewer-form" data-locked="{{ $locked ? '1' : '0' }}" method="POST" action="{{ route('panels.assignments.update', $panel) }}">@csrf @method('PUT')
                            <div class="row g-1">@foreach($reviewers as $reviewer)<div class="col-lg-6"><label class="border rounded p-2 w-100 d-flex align-items-start gap-2"><input type="checkbox" class="form-check-input mt-1 reviewer-checkbox" name="reviewer_ids[]" value="{{ $reviewer->id }}" {{ in_array($reviewer->id, $assignedIds, true) ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}><span><span class="d-block">{{ trim($reviewer->name.' '.$reviewer->lastname.' '.$reviewer->second_lastname) }}</span><small class="text-muted d-block">{{ $reviewer->email }}</small><small class="text-info">{{ $reviewer->assigned_panels_count }} panels assigned</small></span></label></div>@endforeach</div>
                            <small class="selection-count text-muted">{{ count($assignedIds) }} / 2 selected</small>
                            @if(count($assignedIds) > 2)<small class="d-block text-warning">This panel already has more than 2 reviewers. Existing evaluations are preserved; reduce the selection only if assignments are unlocked.</small>@endif
                            @if($locked)<small class="d-block text-muted">Assignments are locked after an evaluation is submitted.</small>@endif
                        </form></td>
                        <td><button form="panel-reviewers-{{ $panel->id }}" type="submit" class="btn btn-primary btn-sm" {{ $locked || $reviewers->isEmpty() ? 'disabled' : '' }}>Save</button></td>
                    </tr>
                @empty<tr><td colspan="4" class="text-center py-4">No panels were found.</td></tr>@endforelse
            </tbody></table></div>
            <div class="mt-3">{{ $panels->onEachSide(1)->links() }}</div>
        </div>
    </div>
</div></div></div></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.panel-reviewer-form').forEach(function (form) {
        if (form.dataset.locked === '1') return;
        const boxes = Array.from(form.querySelectorAll('.reviewer-checkbox'));
        const count = form.querySelector('.selection-count');
        function refresh() {
            const selected = boxes.filter(function (box) { return box.checked; }).length;
            count.textContent = selected + ' / 2 selected';
            boxes.forEach(function (box) { box.disabled = selected >= 2 && !box.checked; });
        }
        boxes.forEach(function (box) { box.addEventListener('change', refresh); });
        refresh();
    });
});
</script>
@endsection
