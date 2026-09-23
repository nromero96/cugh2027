@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-4 px-3">
                        <h4 class="px-0 mb-1">Assigned Abstracts</h4>
                        <p class="text-muted mb-3">Review the abstracts assigned to you and track your evaluations.</p>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="work-list">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Main Author</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Last Update</th>
                                        <th scope="col">Your evaluation</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($abstracts as $abstract)
                                        <tr>
                                            <td>
                                                <a href="{{ route('abstract_posts.show', ['abstract_post' => $abstract->id, 'from' => 'assigned']) }}" class="text-primary text-decoration-underline"><b>{{ $abstract->id }}</b></a>
                                            </td>
                                            <td>
                                                <span class="d-block">{{ trim(($abstract->main_author['name'] ?? '').' '.($abstract->main_author['lastname'] ?? '')) ?: '—' }}</span>
                                                @if($abstract->user)
                                                    <small class="text-muted">({{ $abstract->user->email }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light-secondary text-capitalize">{{ $abstract->presentation_type ?: '—' }}</span><br>
                                                {{ $abstract->abstract_type }}
                                            </td>
                                            <td>
                                                <a href="{{ route('abstract_posts.show', ['abstract_post' => $abstract->id, 'from' => 'assigned']) }}" title="{{ $abstract->title }}">{{ \Illuminate\Support\Str::limit($abstract->title ?: 'Untitled abstract', 20) }}</a>
                                            </td>
                                            <td>
                                                @if($abstract->status === 'draft')
                                                    <span class="badge badge-light-warning text-capitalize">In progress</span>
                                                @elseif($abstract->status === 'submitted')
                                                    <span class="badge badge-light-info text-capitalize">Submitted</span>
                                                @elseif($abstract->status === 'qualified')
                                                    <span class="badge badge-light-primary text-capitalize">Qualified</span>
                                                @elseif($abstract->status === 'accepted')
                                                    <span class="badge badge-light-success text-capitalize">Accepted</span>
                                                @elseif($abstract->status === 'rejected')
                                                    <span class="badge badge-light-danger text-capitalize">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $abstract->updated_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if($abstract->pivot->average_score !== null)
                                                    <span class="badge badge-light-success">Evaluated</span>
                                                    <small class="d-block text-muted">Average: {{ number_format($abstract->pivot->average_score, 2) }} / 10</small>
                                                @else
                                                    <span class="badge badge-light-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('abstract_posts.show', ['abstract_post' => $abstract->id, 'from' => 'assigned']) }}" class="badge badge-light-primary text-start me-2 action-show bs-tooltip" data-toggle="tooltip" data-placement="top" title="View abstract and evaluation" aria-label="View abstract and evaluation">
                                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6z"></path></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">You have no assigned abstracts.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($abstracts->hasPages())
                            <div class="mt-3">{{ $abstracts->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
