@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-12">
                                <h4>Edit Hotel Reservation #{{ $hotelreservation->id }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        @if($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Participant</small>
                                        <strong>{{ trim($hotelreservation->user->name.' '.$hotelreservation->user->lastname.' '.$hotelreservation->user->second_lastname) }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">E-mail</small>
                                        <strong>{{ $hotelreservation->user->email }}</strong>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">Participant information cannot be modified from this form.</small>
                            </div>
                        </div>

                        <form class="row g-3" action="{{ route('hotelreservations.update', $hotelreservation->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label for="hotel_name" class="form-label fw-bold">Hotel <span class="text-danger">*</span></label>
                                <select name="hotel_name" id="hotel_name" class="form-select" required>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel }}" {{ old('hotel_name', $hotelreservation->hotel_name) === $hotel ? 'selected' : '' }}>{{ $hotel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="habitacion_type" class="form-label fw-bold">Room type <span class="text-danger">*</span></label>
                                <select name="habitacion_type" id="habitacion_type" class="form-select" required>
                                    @foreach($roomTypes as $roomType)
                                        <option value="{{ $roomType }}" {{ old('habitacion_type', $hotelreservation->habitacion_type) === $roomType ? 'selected' : '' }}>{{ $roomType }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="number_guests" class="form-label fw-bold">Number of guests <span class="text-danger">*</span></label>
                                <select name="number_guests" id="number_guests" class="form-select" required>
                                    @foreach([1, 2, 3] as $guests)
                                        <option value="{{ $guests }}" {{ (string) old('number_guests', $hotelreservation->number_guests) === (string) $guests ? 'selected' : '' }}>{{ $guests }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="check_in" class="form-label fw-bold">Check-in <span class="text-danger">*</span></label>
                                <input type="date" name="check_in" id="check_in" class="form-control" min="{{ \App\Models\HotelReservation::BOOKING_CHECK_IN_MIN }}" max="{{ \App\Models\HotelReservation::BOOKING_CHECK_IN_MAX }}" value="{{ old('check_in', $hotelreservation->check_in) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="check_out" class="form-label fw-bold">Check-out <span class="text-danger">*</span></label>
                                <input type="date" name="check_out" id="check_out" class="form-control" min="{{ \App\Models\HotelReservation::BOOKING_CHECK_OUT_MIN }}" max="{{ \App\Models\HotelReservation::BOOKING_CHECK_OUT_MAX }}" value="{{ old('check_out', $hotelreservation->check_out) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="comment" class="form-label fw-bold">Participant comments</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4" maxlength="2000">{{ old('comment', $hotelreservation->comment) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="note" class="form-label fw-bold">Internal note</label>
                                <textarea name="note" id="note" class="form-control" rows="4" maxlength="2000">{{ old('note', $hotelreservation->note) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    @foreach(['Pendiente' => 'Pending', 'Atendido' => 'Attended', 'Reservado' => 'Reserved', 'Rechazado' => 'Rejected'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $hotelreservation->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('hotelreservations.show', $hotelreservation->id) }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    if (!checkIn || !checkOut) return;

    function updateMinimumCheckout() {
        if (checkIn.value) {
            const nextDay = new Date(checkIn.value + 'T00:00:00Z');
            nextDay.setUTCDate(nextDay.getUTCDate() + 1);
            checkOut.min = nextDay.toISOString().slice(0, 10);
        } else {
            checkOut.min = '{{ \App\Models\HotelReservation::BOOKING_CHECK_OUT_MIN }}';
        }
        if (checkIn.value && checkOut.value && checkOut.value <= checkIn.value) checkOut.value = '';
    }

    checkIn.addEventListener('change', updateMinimumCheckout);
    updateMinimumCheckout();
});
</script>
@endsection
