@extends('layouts.app')


@section('content')

<style>
    .panels-table { table-layout: fixed; width: 100%; }
    .panels-table td { vertical-align: middle; overflow-wrap: anywhere; }
    .panels-table .action-btns .btn-show svg { color: #4c8df5 !important; }

    @media (max-width: 767.98px) {
        .panels-table thead { display: none; }
        .panels-table, .panels-table tbody, .panels-table tr, .panels-table td {
            display: block;
            width: 100%;
        }
        .panels-table tr {
            border: 1px solid #e0e6ed;
            border-radius: .5rem;
            margin-bottom: 1rem;
            padding: .5rem .75rem;
        }
        .panels-table td {
            border: 0;
            border-bottom: 1px solid #eef0f3;
            display: grid;
            grid-template-columns: 90px minmax(0, 1fr);
            gap: .75rem;
            padding: .65rem 0;
            text-align: left !important;
        }



        .panels-table td::before { content: attr(data-label); font-weight: 600; }
        .panels-table td:last-child { border-bottom: 0; }
        .panels-table .empty-panels { display: block; text-align: center !important; }
        .panels-table .empty-panels::before { content: none; }
    }
</style>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-2">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Panels</h4>
                            </div>
                            
                        </div>
                        <div class="row px-3">
                            <div class="col-md-10">
                                <form method="GET" action="{{ route('panels.index') }}" class="row g-2 mb-3">
                                    <div class="col-md-8 col-lg-6">
                                        <input
                                            type="text"
                                            name="search"
                                            id="panelSearch"
                                            class="form-control"
                                            value="{{ request('search') }}"
                                            placeholder="ID, title, contact, email or institution"
                                        >
                                    </div>
                                    <div class="col-md-4 col-lg-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        @if(request()->filled('search'))
                                            <a href="{{ route('panels.index') }}" class="btn btn-outline-secondary">Clear</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-2 text-end mt-1">
                                @if(auth()->user()->hasRole('Administrador'))
                                    <a href="{{ route('panels.exportexcel') }}" class="btn btn-success btn-sm" title="Export all panel data to Excel">
                                        <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i>
                                        Export Excel
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div>
                            <table class="table table-bordered table-hover mb-4 panels-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 8%;">ID</th>
                                        <th style="width: 47%;">Contact</th>
                                        <th class="text-center" style="width: 25%;">Created At</th>
                                        <th class="text-center" style="width: 20%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($panels as $panel)
                                    <tr>
                                        <td class="text-center" data-label="ID"><a href="{{ route('panels.show', $panel->id) }}"><b class="text-primary">{{ $panel->id }}</b></a></td>
                                        <td data-label="Contact">
                                            <strong class="d-block">{{ $panel->contact_name }}</strong>
                                            <a href="mailto:{{ $panel->contact_email }}" class="d-block">{{ $panel->contact_email }}</a>
                                            @if($panel->contact_institution)
                                                <small class="text-muted d-block">{{ $panel->contact_institution }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center" data-label="Created At">{{ $panel->created_at->format('Y/m/d H:i') }}</td>
                                        <td class="text-center" data-label="Action">
                                            <div class="action-btns">
                                                <a href="{{ route('panels.show', $panel->id) }}" class="action-btn btn-show me-2" title="View Panel" aria-label="View Panel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                    </svg>
                                                </a>
                                                @if(auth()->user()->hasRole('Administrador'))
                                                    <a href="{{ route('panels.edit', $panel->id) }}" class="action-btn btn-edit" title="Edit Panel" aria-label="Edit Panel">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4 empty-panels">No panels found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


@endsection
