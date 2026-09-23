<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewerAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;

    public function __construct(User $user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        $name = trim(implode(' ', array_filter([
            $this->user->name,
            $this->user->lastname,
            $this->user->second_lastname,
        ], function ($part) {
            return $part !== null && trim($part) !== '';
        })));

        return $this
            ->subject('CUGH 2027 - Your reviewer account ('.($name ?: $this->user->email).')')
            ->view('emails.reviewer-account-created');
    }
}
