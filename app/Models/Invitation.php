<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    protected $fillable = ['full_name', 'job_position', 'institution', 'passport_number', 'email', 'country', 'phone_code', 'phone','file_name'];
}
