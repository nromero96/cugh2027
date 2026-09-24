<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'salutation',
        'professional_position_academic_title',
        'institution',
        'country',
        'email',
        'review_interest',
        'panel_languages',
        'subthemes',
        'source_file',
        'source_row',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'review_instructions_sent_at' => 'datetime',
    ];

    public function registeredUser()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
