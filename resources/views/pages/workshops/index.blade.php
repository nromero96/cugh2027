@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-4">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-end">
                                @if(auth()->user()->hasRole('Administrador'))
                                    <a href="{{ route('workshops.exportexcel') }}" class="btn btn-success mb-4">Export Excel</a>
                                @endif
                                <a href="{{ route('workshops.registerworkshop') }}" target="_blank" class="btn btn-primary mb-4 ms-3 me-3">{{__("Form Online")}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Institution</th>
                                        <th scope="col">E-mail</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workshops as $workshop)
                                        <tr>
                                            <td>
                                                <a href="{{route('workshops.show', $workshop->id)}}" class="text-primary text-decoration-underline">
                                                <b>#{{$workshop->id}}</b>
                                                </a>
                                            </td>
                                            <td>
                                                {{$workshop->lead_name}}
                                            </td>
                                            <td>
                                                {{$workshop->lead_institution}}
                                            </td>
                                            <td>
                                                {{$workshop->lead_email}}
                                            </td>
                                            <td>
                                                +{{$workshop->lead_phone}}
                                            </td>
                                            
                                            <td class="text-center">
                                                {{$workshop->created_at}}
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
