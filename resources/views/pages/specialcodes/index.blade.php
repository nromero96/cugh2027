@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-3 mb-4">
                            <form action="{{ route('specialcodes.index') }}" method="GET" role="search" class="d-flex flex-wrap align-items-center gap-2">
                                <label for="special-code-search" class="visually-hidden">Search Special Codes</label>
                                <input type="search" id="special-code-search" name="search" class="form-control w-auto" value="{{ $search }}" maxlength="255" placeholder="Code, description, category or email">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                                @if($search !== '')
                                    <a href="{{ route('specialcodes.index') }}" class="btn btn-outline-secondary">Clear</a>
                                @endif
                            </form>
                            <a href="{{ route('specialcodes.create') }}" class="btn btn-primary">New Special Code</a>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">{{__("Código")}}</th>
                                        <th scope="col">Amount / Payment</th>
                                        <th scope="col">Categories</th>
                                        <th scope="col">Usage rule</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Used</th>
                                        <th scope="col">{{__("Descripción")}}</th>
                                        <th scope="col">Expires</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">{{__("")}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($specialcodes as $specialcode)
                                        <tr>
                                            <td>
                                                {{$specialcode->code}}
                                            </td>
                                            <td>
                                                US$ {{ number_format((float) $specialcode->amount, 2) }}<br>
                                                <small>{{ $specialcode->payment_required === 'Si' ? 'Payment required' : 'No payment required' }}</small>
                                            </td>
                                            <td>{{ $specialcode->categories->pluck('name')->join(', ') ?: 'Not configured' }}</td>
                                            <td>
                                                {{ ['limited' => 'Up to quantity', 'single_use' => 'One use total', 'per_email' => 'One per email'][$specialcode->usage_rule] ?? $specialcode->usage_rule }}
                                                @if($specialcode->allowedEmails->isNotEmpty())
                                                    @foreach($specialcode->allowedEmails->sortBy('email') as $allowedEmail)
                                                        <small class="d-block text-break">{{ $allowedEmail->email }}</small>
                                                    @endforeach
                                                @elseif($specialcode->allowed_email)
                                                    <small class="d-block text-break">{{ $specialcode->allowed_email }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{$specialcode->quantity}}
                                            </td>
                                            <td>
                                                {{$specialcode->used_count}}
                                            </td>
                                            <td>
                                                {{$specialcode->description}}
                                            </td>
                                            <td>
                                                {{$specialcode->expiration}}
                                            </td>
                                            <td class="text-center">
                                                {{$specialcode->status}}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('specialcodes.edit', $specialcode->id) }}" class="badge badge-light-primary text-start me-2 action-edit bs-tooltip" data-toggle="tooltip" data-placement="top" title="{{ __("Editar") }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">{{ $search === '' ? 'No Special Codes yet.' : 'No Special Codes match your search.' }}</td>
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
