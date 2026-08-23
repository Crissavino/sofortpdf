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
    /** Recurring price and trial length, so the email can state what the
     *  customer is actually signing up for. Empty monthlyAmount hides the
     *  notice rather than printing a blank or wrong price. */
    public string $monthlyAmount;
    public int $trialDays;

    public function __construct(
        Customer $user,
        string $amount,
        string $orderNumber = '',
        string $monthlyAmount = '',
        int $trialDays = 0
    ) {
        $this->user = $user;
        $this->amount = $amount;
        $this->orderNumber = $orderNumber;
        $this->monthlyAmount = $monthlyAmount;
        $this->trialDays = $trialDays;
    }

    public function build(): self
    {
        return $this->subject(__('email.order_subject'))
            ->view('emails.order-confirmation');
    }
}
