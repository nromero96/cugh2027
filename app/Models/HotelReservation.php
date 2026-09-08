<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReservation extends Model
{
    use HasFactory;

    public const BOOKING_CHECK_IN_MIN = '2027-02-20';
    public const BOOKING_CHECK_IN_MAX = '2027-03-04';
    public const BOOKING_CHECK_OUT_MIN = '2027-02-21';
    public const BOOKING_CHECK_OUT_MAX = '2027-03-05';

    protected $fillable = [
        'user_id',
        'hotel_name',
        'habitacion_type',
        'number_guests',
        'check_in',
        'check_out',
        'comment',
        'note',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
