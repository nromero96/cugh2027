@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Attention!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-8 col-md-8 col-sm-7 mb-2 col-7">
                                <h4>
                                    Reservation Details
                                </h4>
                            </div>
                            @if(Auth::user()->hasRole('Hotelero'))
                                <div class="col-xl-4 col-md-4 col-sm-5 col-5 text-end pt-3 pe-4">
                                    <a href="{{ route('hotelreservations.edit', $hotelreservation->id) }}" class="btn btn-primary btn-sm">Edit Reservation</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">First name</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->user_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Middle name</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->user_lastname }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Last name</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->user_second_lastname }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Phone</label>
                                <p class="form-control bg-light mb-0">{{ trim($hotelreservation->user_phone_code.' '.$hotelreservation->user_phone_number) }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">{{__("WhatsApp")}}</label>
                                <p class="form-control bg-light mb-0">
                                    @if(!empty($hotelreservation->user_whatsapp_number))
                                        @php $whatsappnumlink = preg_replace('/\D+/', '', $hotelreservation->user_whatsapp_code.$hotelreservation->user_whatsapp_number); @endphp
                                        <a href="https://wa.me/{{ $whatsappnumlink }}" target="_blank" rel="noopener noreferrer">{{ trim($hotelreservation->user_whatsapp_code.' '.$hotelreservation->user_whatsapp_number) }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">E-mail</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->user_email }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Hotel</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->hotel_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Room type</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->habitacion_type }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Number of guests</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->number_guests }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Check-in</label>
                                <p class="form-control bg-light mb-0">{{ \Carbon\Carbon::parse($hotelreservation->check_in)->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Check-out</label>
                                <p class="form-control bg-light mb-0">{{ \Carbon\Carbon::parse($hotelreservation->check_out)->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-0">Comments</label>
                                <p class="form-control bg-light mb-0">{{ $hotelreservation->comment }}</p>
                            </div>

                            <div class="col-md-12">
                                @if($hotelreservation->status == 'Reservado')
                                    <span class="badge badge-light-success">Reserved</span>
                                @elseif ($hotelreservation->status == 'Atendido')
                                    <span class="badge badge-light-info">Attended</span>
                                @elseif ($hotelreservation->status == 'Pendiente')
                                    <span class="badge badge-light-warning">Pending</span>
                                @elseif ($hotelreservation->status == 'Rechazado')
                                    <span class="badge badge-light-danger">Rejected</span>
                                @endif
                                 (Last updated: {{ \Carbon\Carbon::parse($hotelreservation->updated_at)->format('Y-m-d H:i:s') }})
                            </div>
                            <hr class="mt-3 mb-0">

                            <div class="col-md-12 mt-1">
                                <label class="form-label fw-bold mb-0">Internal note</label>
                                <p class="form-control bg-light mb-0">
                                    @if($hotelreservation->note != null)
                                        {{ $hotelreservation->note }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <div class="col-12 text-end">
                                <a href="{{ route('hotelreservations.index') }}" class="btn btn-outline-secondary">Back to Reservations</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
