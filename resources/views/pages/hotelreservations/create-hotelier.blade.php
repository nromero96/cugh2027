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
                                <h4>Create Hotel Reservation</h4>
                                <p class="text-muted px-3 mb-3">Only participants with a confirmed registration are available.</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($participants->isEmpty())
                            <div class="alert alert-warning" role="alert">
                                There are no participants with a confirmed registration available for selection.
                            </div>
                        @endif

                        <form class="row g-3" action="{{ route('hotelreservations.hotelier.store') }}" method="POST">
                            @csrf

                            <div class="col-12">
                                <label for="participant_search" class="form-label fw-bold">Find participant</label>
                                <input type="search" id="participant_search" class="form-control" placeholder="Search by name or e-mail" autocomplete="off" {{ $participants->isEmpty() ? 'disabled' : '' }}>
                            </div>

                            <div class="col-12">
                                <label for="user_id" class="form-label fw-bold">Participant <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required {{ $participants->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Select participant...</option>
                                    @foreach($participants as $participant)
                                        <option value="{{ $participant->id }}" {{ (string) old('user_id') === (string) $participant->id ? 'selected' : '' }}>
                                            {{ trim($participant->name.' '.$participant->lastname.' '.$participant->second_lastname) }} — {{ $participant->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted" id="participant_result_count">{{ $participants->count() }} participant(s) available</small>
                            </div>

                            <div class="col-md-6">
                                <label for="hotel_name" class="form-label fw-bold">Hotel <span class="text-danger">*</span></label>
                                <select name="hotel_name" id="hotel_name" class="form-select" required>
                                    <option value="">Select hotel...</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel }}" {{ old('hotel_name') === $hotel ? 'selected' : '' }}>{{ $hotel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="habitacion_type" class="form-label fw-bold">Room type <span class="text-danger">*</span></label>
                                <select name="habitacion_type" id="habitacion_type" class="form-select" required>
                                    <option value="">Select room type...</option>
                                    @foreach($roomTypes as $roomType)
                                        <option value="{{ $roomType }}" {{ old('habitacion_type') === $roomType ? 'selected' : '' }}>{{ $roomType }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="number_guests" class="form-label fw-bold">Number of guests <span class="text-danger">*</span></label>
                                <select name="number_guests" id="number_guests" class="form-select" required>
                                    <option value="">Select...</option>
                                    @foreach([1, 2, 3] as $guests)
                                        <option value="{{ $guests }}" {{ (string) old('number_guests') === (string) $guests ? 'selected' : '' }}>{{ $guests }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="check_in" class="form-label fw-bold">Check-in <span class="text-danger">*</span></label>
                                <input type="date" name="check_in" id="check_in" class="form-control" min="{{ \App\Models\HotelReservation::BOOKING_CHECK_IN_MIN }}" max="{{ \App\Models\HotelReservation::BOOKING_CHECK_IN_MAX }}" value="{{ old('check_in') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="check_out" class="form-label fw-bold">Check-out <span class="text-danger">*</span></label>
                                <input type="date" name="check_out" id="check_out" class="form-control" min="{{ \App\Models\HotelReservation::BOOKING_CHECK_OUT_MIN }}" max="{{ \App\Models\HotelReservation::BOOKING_CHECK_OUT_MAX }}" value="{{ old('check_out') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="comment" class="form-label fw-bold">Comments</label>
                                <textarea name="comment" id="comment" class="form-control" rows="5" maxlength="2000">{{ old('comment') }}</textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('hotelreservations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" {{ $participants->isEmpty() ? 'disabled' : '' }}>Create Reservation</button>
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
    const search = document.getElementById('participant_search');
    const select = document.getElementById('user_id');
    const resultCount = document.getElementById('participant_result_count');
    if (!search || !select) return;

    const options = Array.from(select.options).slice(1);
    search.addEventListener('input', function () {
        const term = this.value.trim().toLocaleLowerCase();
        let visible = 0;
        options.forEach(function (option) {
            const matches = !term || option.text.toLocaleLowerCase().includes(term);
            option.hidden = !matches;
            if (matches) visible++;
        });
        if (select.selectedOptions[0] && select.selectedOptions[0].hidden) select.value = '';
        resultCount.textContent = visible + ' participant(s) found';
    });

    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
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
