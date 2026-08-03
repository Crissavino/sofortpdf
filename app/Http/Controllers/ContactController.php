<?php

namespace App\Http\Controllers;

use App\Mail\ContactAutoReplyMail;
use App\Mail\ContactNotifyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact.show');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot — real users leave this empty
            'website' => ['nullable', 'size:0'],
        ], [
            'name.required'     => __('contact_ui.err_name_required'),
            'email.required'    => __('contact_ui.err_email_required'),
            'email.email'       => __('contact_ui.err_email_invalid'),
            'message.required'  => __('contact_ui.err_message_required'),
            'message.min'       => __('contact_ui.err_message_min'),
            'website.size'      => __('contact_ui.err_generic'),
        ]);

        $ip = $this->clientIp($request);

        // Rate limit by IP: max 5 messages per hour
        $key = 'contact:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()
                ->withErrors(['message' => __('contact_ui.err_rate_limited')])
                ->withInput();
        }

        if (! $this->passesTurnstile($request, $ip)) {
            return back()
                ->withErrors(['captcha' => __('contact_ui.err_captcha')])
                ->withInput();
        }

        RateLimiter::hit($key, 3600);

        $adminEmail = config('contact.email');
        $locale     = app()->getLocale();

        try {
            Mail::to($adminEmail)->send(new ContactNotifyMail(
                $data['name'],
                $data['email'],
                $data['message'],
                $ip,
                $request->userAgent(),
                $locale
            ));

            Mail::to($data['email'])->send(new ContactAutoReplyMail(
                $data['name'],
                $data['message']
            ));
        } catch (\Throwable $e) {
            Log::error('Contact form send failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);
            return back()
                ->withErrors(['message' => __('contact_ui.err_send_failed')])
                ->withInput();
        }

        return back()->with('status', __('contact_ui.success'));
    }

    /**
     * Real visitor IP. Cloudflare sits in front and TrustProxies is not
     * configured, so $request->ip() returns the CF edge IP — useless as a
     * rate-limit key (bots rotate across edges, real visitors collide).
     * Same approach GetIpInformation already uses.
     */
    private function clientIp(Request $request): string
    {
        return $request->server('HTTP_CF_CONNECTING_IP') ?: $request->ip();
    }

    /**
     * Verify the Cloudflare Turnstile token. Fail-closed: a missing token,
     * a rejected token or an unreachable siteverify endpoint all block the send.
     * Returns true when Turnstile is not configured.
     */
    private function passesTurnstile(Request $request, string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');
        if (! $secret) {
            return true;
        }

        $token = $request->input('cf-turnstile-response');
        if (! $token) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->asForm()
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (\Throwable $e) {
            Log::error('Turnstile verification unreachable', [
                'error' => $e->getMessage(),
                'ip'    => $ip,
            ]);
            return false;
        }

        if ($response->json('success') === true) {
            return true;
        }

        Log::error('Turnstile verification rejected', [
            'codes' => $response->json('error-codes'),
            'ip'    => $ip,
        ]);

        return false;
    }
}
