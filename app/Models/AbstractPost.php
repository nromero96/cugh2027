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

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'abstract_post_reviewers', 'abstract_post_id', 'reviewer_id')
            ->withPivot([
                'score_1',
                'score_2',
                'score_3',
                'score_4',
                'score_5',
                'average_score',
                'reviewer_note',
            ])
            ->withTimestamps();
    }

}
