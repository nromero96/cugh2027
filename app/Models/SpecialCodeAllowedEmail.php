<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialCodeAllowedEmail extends Model
{
    public $timestamps = false;

    protected $fillable = ['email'];
}
