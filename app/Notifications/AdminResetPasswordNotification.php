<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPasswordNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Password Reset Request - ' . ($this->user->name ?: 'Unknown User') . ' (' . $this->user->email . ')')
            ->greeting('Hello Administrator,')
            ->line('A user has requested to reset their account password.')
            ->line('User Information:')
            ->line('Name: ' . ($this->user->name ?? 'Not registered'))
            ->line('Email: ' . $this->user->email)
            ->line('Requested At: ' . now()->format('F d, Y h:i A'))
            ->action('Open Password Reset Link', $resetUrl)
            ->line('If the user reports not receiving the password reset email, you may copy and share the password reset link above.')
            ->line('This link will expire according to the application password reset configuration.')
            ->line('This is an automated notification.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
