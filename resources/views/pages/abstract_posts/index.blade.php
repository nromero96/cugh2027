@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @php
                        $user = Auth::user();
                        //get user logged role
                        $userRole = $user->roles->pluck('name')->toArray();
                    @endphp
                    
                    
                    <div class="widget-header pt-4">
                        <div class="row">
                            <div class="col-6">
                                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                    {{-- Export --}}
                                    <a href="{{ route('abstract_posts.exportexcel') }}" class="btn btn-success mb-4 ms-3 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5zM3 12v-2h2v2zm0 1h2v2H4a1 1 0 0 1-1-1zm3 2v-2h3v2zm4 0v-2h3v1a1 1 0 0 1-1 1zm3-3h-3v-2h3zm-7 0v-2h3v2z"/>
                                        </svg> 
                                        Export List</a>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <a href="{{ route('abstract_posts.create') }}" class="btn btn-primary mb-4 ms-3 me-3">New Abstract</a>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="work-list">
                                <thead>
                                    <tr>
                                        <th scope="col">{{__("#") }}</th>
                                        <th scope="col">{{__("Main Author")}}</th>
                                        <th scope="col">{{__("Type")}}</th>
                                        <th scope="col">{{__("Title")}}</th>
                                        <th scope="col">{{__("Status")}}</th>
                                        <th scope="col">{{__("Last Update")}}</th>
                                        <th scope="col">{{__("Action")}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($abstract_posts->isEmpty())

                                        @if( $userRole[0] == 'Calificador')
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <h6 class="mt-2">{{__("He does not yet have any jobs assigned to him to qualify.")}}</h6>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <h6 class="mt-2">{{__("There are no registered abstract")}}</h6>
                                                    <a href="{{ route('abstract_posts.create') }}" class="btn btn-primary mb-4 ms-3 me-3">{{__("New Abstract")}}</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        @foreach ($abstract_posts as $abstrpost)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('abstract_posts.show', $abstrpost->id) }}" class="text-primary text-decoration-underline"> <b>{{$abstrpost->id}}</b> </a>
                                                </td>
                                                <td>
                                                    <span class="d-block">
                                                        {{ $abstrpost->main_author['name'] ?? '' }}
                                                        {{ $abstrpost->main_author['lastname'] ?? '' }}
                                                    </span>
                                                    <small class="text-muted">({{$abstrpost->user->email}})</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-secondary text-capitalize">{{ $abstrpost->presentation_type }}</span><br>
                                                    {{$abstrpost->abstract_type}}
                                                </td>
                                                <td>
                                                    <a href="{{ route('abstract_posts.show', $abstrpost->id) }}" title="{{$abstrpost->title}}">{{ Str::limit($abstrpost->title, 20) }}</span></a>
                                                </td>
                                                <td>
                                                    @if($abstrpost->status == 'draft')
                                                        <span class="badge badge-light-warning text-capitalize">In progress</span>
                                                    @elseif ($abstrpost->status == 'submitted')
                                                        <span class="badge badge-light-info text-capitalize">{{ $abstrpost->status }}</span>
                                                    @elseif ($abstrpost->status == 'accepted')
                                                        <span class="badge badge-light-success text-capitalize">{{ $abstrpost->status }}</span>
                                                    @elseif ($abstrpost->status == 'refused')
                                                        <span class="badge badge-light-danger text-capitalize">{{ $abstrpost->status }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ date('Y-m-d H:i', strtotime($abstrpost->updated_at)) }}
                                                </td>

                                                <td class="text-center">
                                                    @if($abstrpost->status != 'draft' || Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Calificador') || Auth::user()->hasRole('Secretaria'))
                                                        <a href="{{ route('abstract_posts.show', $abstrpost->id) }}" class="badge badge-light-primary text-start me-2 action-show bs-tooltip" data-toggle="tooltip" data-placement="top" title="{{ __("Ver") }}">
                                                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6z"></path></svg>
                                                        </a>
                                                    @endif
                                                    @if($abstrpost->status == 'draft' && $abstrpost->user_id == $user->id)
                                                        <a href="{{ route('abstract_posts.edit', $abstrpost->id) }}" class="badge badge-light-primary text-start me-2 action-edit bs-tooltip" data-toggle="tooltip" data-placement="top" title="{{ __("Editar") }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                        </a>
                                                        <form class="d-inline" action="{{ route('abstract_posts.destroy', $abstrpost->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                                <button type="submit" class="badge badge-light-danger text-start action-delete bs-tooltip" data-toggle="tooltip" data-placement="top" title="{{ __("Eliminar") }}" onclick="return confirm('{{ __('Are you sure you want to delete this abstract?') }}')" >
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                                </button>
                                                        </form>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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

<script>
// JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los formularios de eliminación
    var deleteForms = document.querySelectorAll('.deleteForm');

    // Agregar controlador de eventos de clic a cada botón de eliminación
    deleteForms.forEach(function(form) {
        var deleteButton = form.querySelector('.btn-delete');
        deleteButton.addEventListener('click', function(event) {
            event.preventDefault();
            if (confirm("{{ __('Are you sure you want to delete this user?') }}")) {
                form.submit();
            }
        });
    });
});


</script>