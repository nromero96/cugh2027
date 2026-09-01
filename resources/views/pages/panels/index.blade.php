@extends('layouts.app')


@section('content')

<style>
    .panels-table { table-layout: fixed; width: 100%; }
    .panels-table td { vertical-align: middle; overflow-wrap: anywhere; }
    .panels-table .action-btns .btn-show svg { color: #4c8df5 !important; }
    .panel-search-row { display: flex; flex-wrap: nowrap; width: 100%; }
    .panel-search-row .form-control { min-width: 0; }
    .panel-search-row .btn { flex: 0 0 auto; }

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
            padding: .65rem 1rem;
        }
        .panels-table td {
            border: 0;
            border-bottom: 1px solid #eef0f3;
            display: block;
            padding: .75rem 0;
            text-align: left !important;
            line-height: 1.45;
            min-width: 0;
        }

        .panels-table td::before {
            content: attr(data-label);
            display: block;
            color: #6c757d;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .03em;
            margin-bottom: .3rem;
            text-transform: uppercase;
        }
        .panels-table td a,
        .panels-table td strong,
        .panels-table td small {
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .panels-table td:last-child { border-bottom: 0; }
        .panels-table .action-btns {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: .5rem;
            padding-top: .15rem;
        }
        .panels-table .action-btns a,
        .panels-table .action-btns button {
            align-items: center;
            display: inline-flex;
            justify-content: center;
            min-height: 34px;
            min-width: 38px;
        }
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
                                <h4>{{ $rejectedPage ? 'Rejected Panels' : 'Panels' }}</h4>
                            </div>
                            
                        </div>
                        <div class="row px-3">
                            <div class="col-md-9">
                                <form method="GET" action="{{ $rejectedPage ? route('panels.rejected') : route('panels.index') }}" class="mb-3">
                                    <div class="panel-search-row">
                                        <input
                                            type="text"
                                            name="search"
                                            id="panelSearch"
                                            class="form-control"
                                            value="{{ request('search') }}"
                                            placeholder="ID, title, contact, email or institution"
                                        >
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    @if(request()->filled('search'))
                                        <a href="{{ $rejectedPage ? route('panels.rejected') : route('panels.index') }}" class="small d-inline-block mt-1">Clear search</a>
                                    @endif
                                </form>
                            </div>

                            <div class="col-md-3 text-end mt-0">
                                @if(auth()->user()->hasRole('Administrador'))
                                    @if($rejectedPage)
                                        <a href="{{ route('panels.index') }}" class="btn btn-outline-secondary btn-sm" title="Back to Panels" aria-label="Back to Panels">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('panels.rejected') }}" class="btn btn-danger btn-sm" title="Rejected Panels" aria-label="Rejected Panels">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('panels.exportexcel') }}" class="btn btn-success btn-sm" title="Export all panel data to Excel">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
                                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5zM3 12v-2h2v2zm0 1h2v2H4a1 1 0 0 1-1-1zm3 2v-2h3v2zm4 0v-2h3v1a1 1 0 0 1-1 1zm3-3h-3v-2h3zm-7 0v-2h3v2z"/>
                                        </svg>
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
                                                    @unless($rejectedPage)
                                                        <form action="{{ route('panels.reject', $panel) }}" method="POST" class="d-inline ms-2">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="action-btn btn-delete border-0 bg-transparent p-0" title="Move to Rejected" aria-label="Move to Rejected" onclick="return confirm('Are you sure you want to move this panel to Rejected?')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash text-danger" viewBox="0 0 16 16">
                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2h4V1a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1h4zM6.5 1a.5.5 0 0 0-.5.5V2h4v-.5a.5.5 0 0 0-.5-.5zM4 4v9a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endunless
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
