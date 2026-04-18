<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $user;
    public string $amount;

    public function __construct(Customer $user, string $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    public function build(): self
    {
        return $this->subject(__('email.order_subject'))
            ->view('emails.order-confirmation');
    }
}
