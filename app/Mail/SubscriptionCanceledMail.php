<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCanceledMail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $user;

    public function __construct(Customer $user)
    {
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->subject(__('email.subscription_canceled_subject'))
            ->view('emails.subscription-canceled');
    }
}
