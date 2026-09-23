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
                    <div class="col-md-4">
                        
                    </div>
                    <div class="col-md-4">
                        
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
                                <form method="GET" action="{{ route('reviewer_candidates.index') }}" class="d-flex gap-2 mb-3">
                                    <input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Name, email, institution or country">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </button>
                                    @if($search !== '')
                                        <a href="{{ route('reviewer_candidates.index') }}" class="btn btn-outline-secondary">Clear</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position / Institution</th>
                                        <th>Country</th>
                                        <th>Email</th>
                                        <th>Registered user</th>
                                        <th>Review interest</th>
                                        <th>Panel languages</th>
                                        <th>Subthemes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reviewers as $reviewer)
                                        <tr>
                                            <td style="min-width: 190px;">
                                                <strong>{{ trim($reviewer->salutation.' '.$reviewer->first_name.' '.$reviewer->last_name) ?: '—' }}</strong>
                                            </td>
                                            <td style="min-width: 230px;">
                                                <span class="d-block">{{ $reviewer->professional_position_academic_title ?: '—' }}</span>
                                                <small class="text-muted">{{ $reviewer->institution ?: 'No institution' }}</small>
                                            </td>
                                            <td>{{ $reviewer->country ?: '—' }}</td>
                                            <td><a href="mailto:{{ $reviewer->email }}">{{ $reviewer->email }}</a></td>
                                            <td class="text-center">
                                                @if($reviewer->registeredUser)
                                                    <span class="badge badge-light-success">Yes</span>
                                                @else
                                                    <span class="badge badge-light-secondary d-inline-block mb-2">No</span>
                                                    <form method="POST" action="{{ route('reviewer_candidates.create_user', $reviewer) }}" onsubmit="return confirm('Create a participant account and email the login credentials to {{ addslashes($reviewer->email) }}?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm text-nowrap">Create user</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td style="min-width: 190px;">
                                                <span title="{{ $reviewer->review_interest }}">{{ \Illuminate\Support\Str::limit($reviewer->review_interest, 100) ?: '—' }}</span>
                                            </td>
                                            <td style="min-width: 170px;">
                                                <span title="{{ $reviewer->panel_languages }}">{{ \Illuminate\Support\Str::limit($reviewer->panel_languages, 100) ?: '—' }}</span>
                                            </td>
                                            <td style="min-width: 300px;">
                                                <span title="{{ $reviewer->subthemes }}">{{ \Illuminate\Support\Str::limit($reviewer->subthemes, 160) ?: '—' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
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
