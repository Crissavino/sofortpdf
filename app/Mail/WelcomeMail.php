<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $user;
    public string $password;

    public function __construct(Customer $user, string $password = '')
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build(): self
    {
        return $this->subject(__('email.welcome_subject'))
            ->view('emails.welcome');
    }
}
