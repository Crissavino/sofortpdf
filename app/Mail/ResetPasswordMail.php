<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $user;
    public string $resetUrl;

    public function __construct(Customer $user, string $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }

    public function build(): self
    {
        return $this->subject(__('email.reset_subject'))
            ->view('emails.reset-password');
    }
}
