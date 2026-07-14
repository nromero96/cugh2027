<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AbstractPost;
use App\Models\User;

class AbstractPostNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'abstract_post_id',
        'user_id',
        'comment',
        'status_change',
    ];

    public function abstractPost()
    {
        return $this->belongsTo(AbstractPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
