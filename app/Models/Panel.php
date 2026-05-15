<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;

    protected $fillable = [

        'language',
        'subthemes',
        'subthemes_other',
        'title',

        'contact_salutation',
        'contact_name',
        'contact_institution',
        'contact_country',
        'contact_phone',
        'contact_email',

        'moderator_name',
        'moderator_position',
        'moderator_institution',
        'moderator_country',

        'speakers',

        'description',
        'learning_objectives',
    ];

    protected $casts = [
        'subthemes' => 'array',
        'speakers' => 'array',
    ];
}
