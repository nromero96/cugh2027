@extends('layouts.app')

@section('content')
<style>
.assigned-panels-table { table-layout: fixed; width: 100%; }
.assigned-panels-table th, .assigned-panels-table td { padding: .55rem .7rem; vertical-align: middle; }
.assigned-panels-table .panel-title, .assigned-panels-table .panel-contact { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.assigned-panels-table .panel-title { min-width: 0; }
.assigned-panels-table .panel-id { flex: 0 0 auto; display: inline-block; padding: .2rem .45rem; border-radius: 6px; background: #fff1f2; color: #CC1F2F; font-weight: 700; text-decoration: none; }
.assigned-panels-table .panel-id:hover, .assigned-panels-table .panel-id:focus { background: #CC1F2F; color: #fff; }
.assigned-panels-table .panel-status { flex: 0 0 auto; }
.assigned-panels-table .evaluation-cell { white-space: nowrap; }
.assigned-panels-table .evaluation-link { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin-left: .35rem; border: 1px solid #f4c5cb; border-radius: 6px; background: #fff1f2; color: #CC1F2F; vertical-align: middle; transition: background-color .15s ease, border-color .15s ease, color .15s ease; }
.assigned-panels-table .evaluation-link:hover, .assigned-panels-table .evaluation-link:focus { background: #f7d2d6; border-color: #CC1F2F; color: #CC1F2F; }
@media (max-width: 767.98px) {
    .assigned-panels-table th, .assigned-panels-table td { padding: .5rem; }
    .assigned-panels-table .contact-column { display: none; }
    .assigned-panels-table th:first-child { width: 68% !important; }
    .assigned-panels-table th:last-child { width: 32% !important; }
    .assigned-panels-table .evaluation-cell { white-space: normal; }
    .assigned-panels-table .evaluation-cell small { display: block; }
}
</style>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-3 px-3">
                        <h4 class="px-0 mb-1">Assigned Panels</h4>
                        <p class="text-muted mb-2">{{ $panels->total() }} {{ \Illuminate\Support\Str::plural('panel', $panels->total()) }} assigned</p>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <table class="table table-hover table-striped table-bordered assigned-panels-table mb-0">
                            <thead><tr><th style="width:55%">Panel</th><th class="contact-column" style="width:25%">Contact</th><th style="width:20%">Your evaluation</th></tr></thead>
                            <tbody>
                                @forelse($panels as $panel)
                                    @php $panelUrl = route('panels.show', ['panel' => $panel->id, 'from' => 'assigned']); @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <a href="{{ $panelUrl }}" class="panel-id" title="Open panel #{{ $panel->id }}">#{{ $panel->id }}</a>
                                                <a href="{{ $panelUrl }}" class="panel-title flex-grow-1" title="{{ $panel->title ?: 'Untitled panel' }}">{{ $panel->title ?: 'Untitled panel' }}</a>
                                                <span class="badge badge-light-secondary panel-status">{{ $panel->status }}</span>
                                            </div>
                                            <small class="d-md-none text-muted panel-contact" title="{{ $panel->contact_name }}">{{ $panel->contact_name }}</small>
                                        </td>
                                        <td class="contact-column">
                                            <span class="panel-contact" title="{{ $panel->contact_name }}">{{ $panel->contact_name ?: '—' }}</span>
                                            <small class="panel-contact text-muted" title="{{ $panel->contact_email }}">{{ $panel->contact_email }}</small>
                                        </td>
                                        <td class="evaluation-cell">
                                            @if($panel->pivot->average_score !== null)
                                                <span class="badge badge-light-success">Evaluated</span>
                                                <a href="{{ $panelUrl }}" class="evaluation-link" title="View panel and evaluation" aria-label="View panel #{{ $panel->id }} and evaluation">
                                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                                <small class="text-muted ms-1">{{ number_format($panel->pivot->average_score, 2) }} / 10</small>
                                            @else
                                                <span class="badge badge-light-warning">Pending</span>
                                                <a href="{{ $panelUrl }}" class="evaluation-link" title="View panel and evaluation" aria-label="View panel #{{ $panel->id }} and evaluation">
                                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">You have no assigned panels.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($panels->hasPages())<div class="mt-3">{{ $panels->links() }}</div>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
