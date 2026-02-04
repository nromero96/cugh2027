<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryInscription extends Model
{
    protected $table = 'category_inscriptions';

    protected $fillable = [
        'name',
        'price',
        'price_low'
    ];
}
