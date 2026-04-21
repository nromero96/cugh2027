@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header pt-4">
                        <h4>
                            Member Institutions
                        </h4>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Institution</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Secretaria'))
                                        @foreach ($member_institutions as $institution)
                                            <tr>
                                                <td>
                                                    {{$institution->id}}
                                                </td>
                                                <td>
                                                    {{$institution->name}}
                                                </td>
                                                <td>
                                                    {{$institution->country_name ?? 'N/A'}}
                                                </td>
                                                <td>
                                                    <div class="switch form-switch-custom form-switch-primary">
                                                        <input  
                                                            class="switch-input status-switch" 
                                                            type="checkbox" 
                                                            role="switch"
                                                            data-id="{{ $institution->id }}"
                                                            id="status_{{ $institution->id }}"
                                                            {{ $institution->is_active ? 'checked' : '' }}>
                                                        
                                                        <label class="switch-label" for="status_{{ $institution->id }}"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                <h5 class="text-center">No tienes permiso para acceder a esta sección</h5>
                                            </td>
                                        </tr>
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
document.addEventListener('change', async function (e) {

    if (!e.target.classList.contains('status-switch')) return;

    const checkbox = e.target;
    const id = checkbox.dataset.id;
    const status = checkbox.checked ? 1 : 0;

    // 🔒 bloquear mientras procesa
    checkbox.disabled = true;

    try {
        const response = await fetch("{{ route('member_institutions_update_status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                id: id,
                status: status
            })
        });

        if (!response.ok) throw new Error('Error en servidor');

        const data = await response.json();

        console.log('Actualizado correctamente');

    } catch (error) {
        console.error(error);

        // 🔁 revertir si falla
        checkbox.checked = !checkbox.checked;
        alert('No se pudo actualizar');
    }

    // 🔓 desbloquear
    checkbox.disabled = false;

});
</script>