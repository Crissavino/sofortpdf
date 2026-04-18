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
    public string $orderNumber;

    public function __construct(Customer $user, string $amount, string $orderNumber = '')
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->orderNumber = $orderNumber;
    }

    public function build(): self
    {
        return $this->subject(__('email.order_subject'))
            ->view('emails.order-confirmation');
    }
}
