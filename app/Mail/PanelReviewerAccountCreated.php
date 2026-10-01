<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PanelReviewerAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;

    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        $name = trim(implode(' ', array_filter([
            $this->user->name, $this->user->lastname, $this->user->second_lastname,
        ], function ($part) { return $part !== null && trim($part) !== ''; })));

        $copy = config('services.correonotificacion.copy');
        if ($copy) {
            $this->replyTo($copy);
            $this->bcc($copy);
        }

        return $this->subject('CUGH LIMA 2027 Panel Reviewer Account - ('.($name ?: $this->user->email).')')
            ->view('emails.panel-reviewer-account-created');
    }
}
