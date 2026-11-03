<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractPostEmail extends Model
{
    protected $fillable = [
        'abstract_post_id',
        'sent_by_user_id',
        'kind',
        'recipient_email',
        'from_email',
        'reply_to_email',
        'subject',
        'body_html',
        'attachment_disk',
        'attachment_path',
        'attachment_filename',
        'attachment_mime',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function abstractPost()
    {
        return $this->belongsTo(AbstractPost::class);
    }
}
