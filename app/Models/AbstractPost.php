<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Country;

class AbstractPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'presentation_type',
        'title',
        'main_author',
        'main_author_country_id',
        'co_authors',
        'institutions',
        'abstract_type',
        'subtopic',
        'body',
        'keywords',
        'status',
    ];

    protected $casts = [
        'main_author' => 'array',
        'co_authors' => 'array',
        'institutions' => 'array',
        'keywords' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mainAuthorCountry()
    {
        return $this->belongsTo(
            Country::class,
            'main_author_country_id'
        );
    }

    public function notes()
    {
        return $this->hasMany(AbstractPostNote::class)
            ->orderBy('created_at', 'desc');
    }

}
