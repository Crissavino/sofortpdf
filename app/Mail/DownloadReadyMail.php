<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DownloadReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $user;
    public string $filename;
    public string $downloadUrl;

    public function __construct(Customer $user, string $filename, string $downloadUrl)
    {
        $this->user = $user;
        $this->filename = $filename;
        $this->downloadUrl = $downloadUrl;
    }

    public function build(): self
    {
        return $this->subject(__('email.download_ready_subject'))
            ->view('emails.download-ready');
    }
}
