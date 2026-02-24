<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_inscription_id',
        'price_category',
        'total',
        'special_code',
        'document_file',
        'invoice',
        'invoice_type',
        'invoice_type_document',
        'invoice_ruc',
        'invoice_social_reason',
        'invoice_address',
        'payment_method',
        'voucher_file',
        'status',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inscription) {
            $inscription->token = (string) Str::uuid();
        });
    }

}
