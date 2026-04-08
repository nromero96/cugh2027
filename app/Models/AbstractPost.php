<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AbstractPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'presentation_type',
        'title',
        'co_authors',
        'institutions',
        'abstract_type',
        'subtopic',
        'body',
        'keywords',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
