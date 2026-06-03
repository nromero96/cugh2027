<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_name',
        'lead_institution',
        'lead_title',
        'lead_email',
        'lead_phone',
        'lead_cell',

        'workshop_title',
        'workshop_desc',
        'workshop_objectives',
        'workshop_speakers',

        'time_slot',
        'day_length',
        'room_setup',
        'attendees',
        'notes',

        'payment_lead_same',
        'payment_name',
        'payment_institution',
        'payment_title',
        'payment_email',
        'payment_phone',
        'payment_cell',

        'signature_path',
        'place_date',
    ];

}
