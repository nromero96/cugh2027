<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractReviewInstructions extends Mailable
{
    use Queueable, SerializesModels;

    private $reviewerName;

    public function __construct(string $reviewerName)
    {
        $this->reviewerName = trim(preg_replace('/\s+/u', ' ', $reviewerName));
    }

    public function build()
    {
        $replyTo = config('services.correonotificacion.copy');
        if ($replyTo) {
            $this->replyTo($replyTo);
            $this->bcc($replyTo);
        }

        return $this
            ->subject('CUGH LIMA 2027 Abstract Review Process - ('.$this->reviewerName.')')
            ->view('emails.abstract-review-instructions');
    }
}
