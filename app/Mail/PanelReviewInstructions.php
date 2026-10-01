<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PanelReviewInstructions extends Mailable
{
    use Queueable, SerializesModels;

    private $reviewerName;

    public function __construct(string $reviewerName)
    {
        $this->reviewerName = trim(preg_replace('/\s+/u', ' ', $reviewerName));
    }

    public function build()
    {
        $copy = config('services.correonotificacion.copy');
        if ($copy) {
            $this->replyTo($copy);
            $this->bcc($copy);
        }

        return $this->subject('CUGH LIMA 2027 Panel Review Process - ('.$this->reviewerName.')')
            ->view('emails.panel-review-instructions');
    }
}
