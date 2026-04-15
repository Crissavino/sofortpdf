<?php

namespace App\Http\Controllers;

use App\Mail\ContactAutoReplyMail;
use App\Mail\ContactNotifyMail;
use Illuminate\Http\Request;
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

        // Rate limit by IP: max 5 messages per hour
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()
                ->withErrors(['message' => __('contact_ui.err_rate_limited')])
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
                $request->ip(),
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
}
