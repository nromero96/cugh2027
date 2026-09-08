<?php

namespace App\Http\Requests;

use App\Models\HotelReservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreHotelReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'hotel_name' => ['required', 'string', 'max:255'],
            'habitacion_type' => ['required', 'string', 'max:255'],
            'number_guests' => ['required', 'integer', 'between:1,3'],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.HotelReservation::BOOKING_CHECK_IN_MIN, 'before_or_equal:'.HotelReservation::BOOKING_CHECK_IN_MAX],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in', 'after_or_equal:'.HotelReservation::BOOKING_CHECK_OUT_MIN, 'before_or_equal:'.HotelReservation::BOOKING_CHECK_OUT_MAX],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_in.after_or_equal' => 'The check-in date must be on or after February 20, 2027.',
            'check_in.before_or_equal' => 'The check-in date must be on or before March 4, 2027.',
            'check_out.after' => 'The check-out date must be after the check-in date.',
            'check_out.after_or_equal' => 'The check-out date must be on or after February 21, 2027.',
            'check_out.before_or_equal' => 'The check-out date must be on or before March 5, 2027.',
        ];
    }
}
