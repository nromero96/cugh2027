@extends('layouts.app')


@section('content')

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
                            <div class="col-md-8">
                                <h4>Panels</h4>
                            </div>
                            <div class="col-md-4 text-end mt-1">
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Company</th>
                                        <th class="text-center">Created At</th>
                                        <th class="text-center">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($panels as $panel)
                                    <tr>
                                        <td class="text-center">{{ $panel->id }}</td>
                                        <td class="text-center">{{ $panel->contact_name }}</td>
                                        <td class="text-center">{{ $panel->contact_email }}</td>
                                        <td class="text-center">{{ $panel->contact_institution }}</td>
                                        <td class="text-center">{{ $panel->created_at->diffForHumans() }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('panels.show', $panel->id) }}" class="btn btn-primary btn-sm">Ver</a>
                                        </td>
                                    </tr>
                                    @endforeach
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