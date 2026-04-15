<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $contactName;
    public string $contactMessage;

    public function __construct(string $contactName, string $contactMessage)
    {
        $this->contactName    = $contactName;
        $this->contactMessage = $contactMessage;
    }

    public function build(): self
    {
        return $this->subject(__('email.contact_autoreply_subject'))
            ->view('emails.contact-autoreply');
    }
}
