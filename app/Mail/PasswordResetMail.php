<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $resetUrl = config('app.url') . "/password-reset-form?token={$this->token}&email=" . urlencode($this->user->email);

        return $this->subject('Reset Your Password')
                    ->view('emails.password-reset')
                    ->with(['user' => $this->user, 'resetUrl' => $resetUrl]);
    }
}
