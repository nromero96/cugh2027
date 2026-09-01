@extends('layouts.app')


@section('content')

<style>
    #inscrip-list.columns-resized {
        table-layout: fixed;
    }

    #inscrip-list thead th {
        position: relative;
        overflow: visible;
        background-color: #ffffff;
    }

    #inscrip-list tbody td {
        overflow: hidden;
        background-color: #ffffff;
    }

    #inscrip-list.table-striped tbody tr:nth-of-type(odd) > td {
        background-color: #f7f8fa;
    }

    #inscrip-list.table-hover tbody tr:hover > td {
        background-color: #eef2f7;
    }

    #inscrip-list .column-resizer {
        position: absolute;
        top: 0;
        right: -4px;
        z-index: 2;
        width: 8px;
        height: 100%;
        cursor: col-resize;
        touch-action: none;
        user-select: none;
    }

    #inscrip-list .column-resizer:hover,
    #inscrip-list .column-resizer.is-resizing {
        border-right: 2px solid #4361ee;
    }
</style>


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-top-spacing layout-spacing">
                
                @php
                    $user = Auth::user();
                    //get user logged role
                    $userRole = $user->roles->pluck('name')->toArray();
                @endphp
                    

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Good going!</strong> 
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Attention!</strong> 
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($userRole[0] == 'Participante')

                        @foreach ($inscriptions as $inscription)
                            <div class="card mb-3 mb-sm-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2 position-relative">
                                            <h5 class="text-center">ID: # <a href="{{ route('inscriptions.show', $inscription->id)}}" class="text-info">{{$inscription->id}}</a></h5>
                                            <hr>
                                            <div class="text-center">
                                            {{ $inscription->created_at }}
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <h5>{{$inscription->user_name.' '.$inscription->user_lastname.' '.$inscription->user_second_lastname}}</h5>
                                            <p>{{$inscription->user_country}}</p>
                                            <p>{{$inscription->category_inscription_name}}</p>
                                            <p>US$ {{$inscription->total}}</p>
                                            <div>
                                                @php 
                                                    if($inscription->payment_method == 'none'){
                                                        $payment_method = 'No payment required';
                                                    }else{
                                                        $payment_method = $inscription->payment_method;
                                                    }
                                                @endphp

                                                @if($inscription->status == 'Paid')
                                                    <span class="badge badge-light-secondary">{{ $inscription->status .' ('.$payment_method.')' }}</span>
                                                @elseif ($inscription->status == 'Confirmed')
                                                    <span class="badge badge-light-success">{{ $inscription->status .' ('.$payment_method.')' }}</span>
                                                @elseif ($inscription->status == 'Processing')
                                                    <span class="badge badge-light-info">{{ $inscription->status .' ('.$payment_method.')' }}</span>
                                                @elseif ($inscription->status == 'Pending')
                                                    <span class="badge badge-light-warning">{{ $inscription->status .' ('.$payment_method.')' }}</span>
                                                    @if($inscription->payment_method == 'Credit/Debit Card' && $inscription->total > 0 && ($inscription->special_code == '' || $inscription->price_accompanist > 0) )
                                                        <a href="{{ url(config('services.upch.url_send_data') . '/' . $inscription->token) }}" class="btn btn-primary me-1 btn-sm px-2 py-1">{{__("Pay")}}</a>
                                                    @endif
                                                @elseif ($inscription->status == 'Refused')
                                                    <span class="badge badge-light-danger">{{ $inscription->status .' ('.$payment_method.')' }}</span>
                                                @endif
                                            </div>

                                        </div>

                                        <div  class="col-md-3 text-end">
                                            @if($inscription->status == 'Paid' || $inscription->status == 'Processing' || $inscription->status == 'Confirmed')
                                                <a href="{{ route('inscriptions.show', $inscription->id)}}" class="btn btn-primary">
                                                    VIEW <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                                                        </svg></a>
                                            @else
                                            <a href="{{ route('inscriptions.myinscription')}}" class="btn btn-primary ">
                                                CONTINUE <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                                                </svg>
                                            </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    @else
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header pb-2 pt-2">
                                <form action="{{ route('inscriptions.index') }}" method="GET" class="mb-0" >
                                    <div class="row">
                                        <div class="col-md-2 align-self-center">
                                            <h4>Registrations</h4>
                                        </div>
                                        <div class="col-md-1 align-self-center ps-0">
                                            <select name="listforpage" class="form-select form-control-sm ms-0" id="listforpage" onchange="this.form.submit()">
                                                <option value="10" {{ request('listforpage') == 10 ? 'selected' : '' }}>10</option>
                                                <option value="20" {{ request('listforpage') == 20 ? 'selected' : '' }}>20</option>
                                                <option value="50" {{ request('listforpage') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('listforpage') == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 align-self-center">

                                            @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                                
                                                <a href="{{ route('inscriptions.rejects') }}" class="btn btn-danger px-2">
                                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
                                                </a>

                                                <a href="{{ route('inscriptions.exportexcel') }}" class="btn btn-success">
                                                    Excel
                                                </a>

                                            @endif

                                        </div>
                                        <div class="col-md-5 align-self-center text-end">
                                            <div class="input-group">
                                                <input type="text" class="form-control mb-2 mb-md-0" name="search" placeholder="Search..." value="{{ request('search') }}">
                                                @if(request('search') != '')
                                                    <a href="{{ route('inscriptions.index') }}" class="btn btn-outline-light px-1" id="button-addon2" style="border-left: 0px;border-color: #bfc9d4;background: white;">
                                                        <svg width="24" height="24" fill="none" stroke="#9e9e9e" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 2a10 10 0 1 0 0 20 10 10 0 1 0 0-20z"></path>
                                                            <path d="m15 9-6 6"></path>
                                                            <path d="m9 9 6 6"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <button type="submit" class="btn btn-primary" id="button-addon2">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="widget-content widget-content-area pt-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-bordered mb-0" id="inscrip-list">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{__("ID")}}</th>
                                                <th scope="col">{{__("Participant")}}</th>
                                                <th scope="col">{{__("Country")}}</th>
                                                <th scope="col">{{__("Category")}}</th>
                                                <th scope="col">{{__("Payment")}}</th>
                                                <th scope="col">{{__("Status")}}</th>
                                                <th scope="col" style="min-width: 150px;">
                                                    @php
                                                        $completionDirection = request('sort') === 'completion' && request('direction') === 'desc' ? 'asc' : 'desc';
                                                    @endphp
                                                    <a href="{{ route('inscriptions.index', array_merge(request()->query(), ['sort' => 'completion', 'direction' => $completionDirection, 'page' => 1])) }}"
                                                        class="text-dark text-decoration-none"
                                                        title="Sort by completion">
                                                        {{__("Completion")}}
                                                        @if(request('sort') === 'completion')
                                                            <span aria-hidden="true">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                        @else
                                                            <span class="text-muted" aria-hidden="true">↑↓</span>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th scope="col">{{__("Date")}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($inscriptions->isEmpty())
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <h6 class="mt-2">There are no registrations recorded.</h6>
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($inscriptions as $inscription)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('inscriptions.show', $inscription->id)}}" class="text-info">#{{$inscription->id}}</a>
                                                            @if(Auth::user()->hasRole('Administrador') && $inscription->status !== 'Confirmed')
                                                                <a href="{{ route('inscriptions.edit', $inscription->id) }}" class="ms-2" title="Edit registration" aria-label="Edit registration">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{$inscription->user_name.' '.$inscription->user_lastname.' '.$inscription->user_second_lastname}}
                                                            @if($inscription->user_name == '' && $inscription->user_lastname == '' && $inscription->user_second_lastname == '')
                                                                <span class="text-muted">Unnamed</span>
                                                            @endif
                                                            <br>
                                                            <small class="text-info" style="font-size: 10px;">{{ $inscription->user_email }}</small>
                                                        </td>
                                                        <td>
                                                            {{$inscription->user_country}}
                                                        </td>
                                                        <td class="pt-0 pb-0">
                                                            {{ strlen($inscription->category_inscription_name) > 13 ? substr($inscription->category_inscription_name, 0, 14) . '...' : $inscription->category_inscription_name }}
                                                            @if($inscription->special_code != '')
                                                                <br><small class="text-info" style="font-size: 10px;">{{ $inscription->special_code }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            US$ {{$inscription->total}}
                                                        </td>
                                                        <td>
                                                            @php
                                                                if($inscription->payment_method == 'Credit/Debit Card'){
                                                                    $textmp = 'TC';
                                                                }else{
                                                                    $textmp = 'DT';
                                                                }
                                                            @endphp

                                                            @if($inscription->status == 'Paid')
                                                                <span class="badge badge-light-secondary">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                            @elseif ($inscription->status == 'Confirmed')
                                                                <span class="badge badge-light-success">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                            @elseif ($inscription->status == 'Processing')
                                                                <span class="badge badge-light-info">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                            @elseif ($inscription->status == 'Pending')
                                                                <span class="badge badge-light-warning">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                                @if($inscription->payment_method == 'Credit/Debit Card' && $inscription->total > 0 && ($inscription->special_code == '' || $inscription->price_accompanist > 0) )
                                                                    <a href="{{ url(config('services.upch.url_send_data') . '/' . $inscription->token) }}" class="btn btn-primary me-1 btn-sm px-2 py-1">{{__("Pay")}}</a>
                                                                @endif

                                                            @elseif ($inscription->status == 'Draft')
                                                                <span class="badge badge-light-primary">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                            @elseif ($inscription->status == 'Refused')
                                                                <span class="badge badge-light-danger">{{ $inscription->status .' ('.$textmp.')' }}</span>
                                                            @endif

                                                            @if($inscription->compr_pdf)
                                                                <a href="{{ asset('storage/uploads/invoices/' . $inscription->compr_pdf) }}" class="btn btn-success me-1 btn-sm px-2 py-1">INV.</a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($inscription->status === 'Draft' && isset($inscription->completion))
                                                                @php
                                                                    $completion = $inscription->completion;
                                                                    $progressColor = $completion['percentage'] === 100
                                                                        ? 'bg-success'
                                                                        : ($completion['percentage'] >= 70 ? 'bg-info' : ($completion['percentage'] >= 40 ? 'bg-warning' : 'bg-danger'));
                                                                    $missingText = implode(', ', $completion['missing']);
                                                                @endphp
                                                                <div title="{{ $missingText ? 'Missing: '.$missingText : 'All required information is complete.' }}">
                                                                    <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                                                        <span class="fw-semibold">{{ $completion['percentage'] }}%</span>
                                                                        <span class="text-muted">{{ $completion['completed'] }}/{{ $completion['total'] }}</span>
                                                                    </div>
                                                                    <div class="progress" style="height: 7px;">
                                                                        <div class="progress-bar {{ $progressColor }}"
                                                                            role="progressbar"
                                                                            style="width: {{ $completion['percentage'] }}%;"
                                                                            aria-valuenow="{{ $completion['percentage'] }}"
                                                                            aria-valuemin="0"
                                                                            aria-valuemax="100">
                                                                        </div>
                                                                    </div>
                                                                    <small class="d-block mt-1 {{ $completion['percentage'] === 100 ? 'text-success' : 'text-muted' }}" style="font-size: 10px; line-height: 1.2;">
                                                                        {{ $completion['percentage'] === 100 ? 'Ready to submit' : count($completion['missing']).' sections pending' }}
                                                                    </small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">&mdash;</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <span class="d-block">{{ $inscription->created_at->format('Y-m-d') }}</span>
                                                            <small class="text-muted">{{ $inscription->created_at->format('H:i:s') }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mx-0 mt-1">
                                    <div class="col-md-7">
                                        <div class="">
                                            {{ $inscriptions->onEachSide(1)->withQueryString()->links() }}
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-1">
                                        <p class="text-end">Mostrando página {{ $inscriptions->currentPage() }} de {{ $inscriptions->lastPage() }} ({{ $inscriptions->total() }})</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                
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

    initializeResizableRegistrationColumns();
});

function initializeResizableRegistrationColumns() {
    var table = document.getElementById('inscrip-list');
    if (!table) {
        return;
    }

    var headers = Array.prototype.slice.call(table.querySelectorAll('thead th'));
    var storageKey = 'registrations-column-widths-{{ Auth::id() }}';
    var minimumWidth = 70;

    function applyWidths(widths) {
        if (!Array.isArray(widths) || widths.length !== headers.length) {
            return;
        }

        var totalWidth = 0;
        headers.forEach(function(header, index) {
            var width = Math.max(minimumWidth, Number(widths[index]) || minimumWidth);
            header.style.width = width + 'px';
            header.style.minWidth = width + 'px';
            totalWidth += width;
        });

        table.classList.add('columns-resized');
        table.style.width = totalWidth + 'px';
        table.style.minWidth = totalWidth + 'px';
    }

    try {
        var savedWidths = JSON.parse(localStorage.getItem(storageKey));
        applyWidths(savedWidths);
    } catch (error) {
        localStorage.removeItem(storageKey);
    }

    headers.forEach(function(header, columnIndex) {
        var resizer = document.createElement('span');
        resizer.className = 'column-resizer';
        resizer.title = 'Drag to resize. Double-click to reset all columns.';
        resizer.setAttribute('aria-hidden', 'true');
        header.appendChild(resizer);

        resizer.addEventListener('pointerdown', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var initialWidths = headers.map(function(item) {
                return Math.round(item.getBoundingClientRect().width);
            });
            applyWidths(initialWidths);

            var startX = event.clientX;
            var startWidth = initialWidths[columnIndex];
            resizer.classList.add('is-resizing');
            resizer.setPointerCapture(event.pointerId);

            function resize(pointerEvent) {
                initialWidths[columnIndex] = Math.max(minimumWidth, startWidth + pointerEvent.clientX - startX);
                applyWidths(initialWidths);
            }

            function finish() {
                resizer.classList.remove('is-resizing');
                resizer.removeEventListener('pointermove', resize);
                resizer.removeEventListener('pointerup', finish);
                resizer.removeEventListener('pointercancel', finish);
                localStorage.setItem(storageKey, JSON.stringify(initialWidths.map(Math.round)));
            }

            resizer.addEventListener('pointermove', resize);
            resizer.addEventListener('pointerup', finish);
            resizer.addEventListener('pointercancel', finish);
        });

        resizer.addEventListener('dblclick', function(event) {
            event.preventDefault();
            event.stopPropagation();
            localStorage.removeItem(storageKey);
            headers.forEach(function(item) {
                item.style.removeProperty('width');
                item.style.removeProperty('min-width');
            });
            table.classList.remove('columns-resized');
            table.style.removeProperty('width');
            table.style.removeProperty('min-width');
        });
    });
}

</script>
