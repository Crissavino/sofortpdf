<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactNotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $contactName;
    public string $contactEmail;
    public string $contactMessage;
    public ?string $contactIp;
    public ?string $contactUserAgent;
    public string $contactLocale;

    public function __construct(
        string $contactName,
        string $contactEmail,
        string $contactMessage,
        ?string $contactIp = null,
        ?string $contactUserAgent = null,
        string $contactLocale = 'de'
    ) {
        $this->contactName      = $contactName;
        $this->contactEmail     = $contactEmail;
        $this->contactMessage   = $contactMessage;
        $this->contactIp        = $contactIp;
        $this->contactUserAgent = $contactUserAgent;
        $this->contactLocale    = $contactLocale;
    }

    public function build(): self
    {
        return $this->subject('[Kontakt] ' . $this->contactName . ' — sofortpdf.com')
            ->replyTo($this->contactEmail, $this->contactName)
            ->view('emails.contact-notify');
    }
}
