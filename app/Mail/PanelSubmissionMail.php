<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PanelSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $panel;

    public function __construct($panel)
    {
        $this->panel = $panel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('CUGH 2027 - Panel Submission (' . $this->panel->contact_name . ')')
            ->view('emails.panel-submission');
    }
}
