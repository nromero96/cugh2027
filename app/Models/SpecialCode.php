<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'description',
        'status',
        'payment_required',
        'quantity',
        'expiration',
        'usage_rule',
        'allowed_email',
    ];

    public function categories()
    {
        return $this->belongsToMany(CategoryInscription::class, 'special_code_categories');
    }

    public function allowedEmails()
    {
        return $this->hasMany(SpecialCodeAllowedEmail::class);
    }
}
