<?php

namespace App\Services;

use App\Mail\ContactAutoReplyMail;
use App\Mail\ContactNotifyMail;
use App\Mail\DownloadReadyMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\PaymentFailedMail;
use App\Mail\ResetPasswordMail;
use App\Mail\SubscriptionActiveMail;
use App\Mail\SubscriptionCanceledMail;
use App\Mail\TrialEndingMail;
use App\Mail\TrialStartedMail;
use App\Mail\WelcomeMail;
use App\Models\Customer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Centralized email service — all outbound emails go through here.
 * Same pattern as contract-kit's EmailService. Uses try/catch + logging
 * so email failures never crash the main flow.
 */
class EmailService
{
    private function withLocale(string $locale, callable $callback): void
    {
        $original = App::getLocale();
        App::setLocale($locale);
        try {
            $callback();
        } finally {
            App::setLocale($original);
        }
    }

    public function sendWelcome(Customer $customer, string $plainPassword = '', string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer, $plainPassword) {
                Mail::to($customer->email)->send(new WelcomeMail($customer, $plainPassword));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendWelcome failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendTrialStarted(Customer $customer, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer) {
                Mail::to($customer->email)->send(new TrialStartedMail($customer));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendTrialStarted failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendTrialEnding(Customer $customer, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer) {
                Mail::to($customer->email)->send(new TrialEndingMail($customer));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendTrialEnding failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendSubscriptionActive(Customer $customer, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer) {
                Mail::to($customer->email)->send(new SubscriptionActiveMail($customer));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendSubscriptionActive failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendSubscriptionCanceled(Customer $customer, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer) {
                Mail::to($customer->email)->send(new SubscriptionCanceledMail($customer));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendSubscriptionCanceled failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendPaymentFailed(Customer $customer, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer) {
                Mail::to($customer->email)->send(new PaymentFailedMail($customer));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendPaymentFailed failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendPasswordReset(Customer $customer, string $resetUrl, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer, $resetUrl) {
                Mail::to($customer->email)->send(new ResetPasswordMail($customer, $resetUrl));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendPasswordReset failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendOrderConfirmation(Customer $customer, string $amount, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer, $amount) {
                Mail::to($customer->email)->send(new OrderConfirmationMail($customer, $amount));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendOrderConfirmation failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendDownloadReady(Customer $customer, string $filename, string $downloadUrl, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($customer, $filename, $downloadUrl) {
                Mail::to($customer->email)->send(new DownloadReadyMail($customer, $filename, $downloadUrl));
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendDownloadReady failed', ['error' => $e->getMessage(), 'email' => $customer->email]);
        }
    }

    public function sendContact(string $name, string $email, string $message, string $locale = 'de'): void
    {
        try {
            $this->withLocale($locale, function () use ($name, $email, $message, $locale) {
                Mail::to(config('contact.email'))->send(
                    new ContactNotifyMail($name, $email, $message, null, null, $locale)
                );
                Mail::to($email)->send(
                    new ContactAutoReplyMail($name, $message)
                );
            });
        } catch (\Throwable $e) {
            Log::error('EmailService::sendContact failed', ['error' => $e->getMessage()]);
        }
    }
}
