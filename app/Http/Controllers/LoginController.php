<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            // Reuse the password-reset form's validation strings — same
            // wording, already translated in all 5 locale auth_ui files.
            'email.required'    => __('auth_ui.reset_err_email_required'),
            'email.email'       => __('auth_ui.reset_err_email_invalid'),
            'password.required' => __('auth_ui.reset_err_password_required'),
        ]);

        // Customers can sign up across multiple brands with the same email,
        // so scope login to this brand only via website_id.
        $credentials = $request->only('email', 'password');
        $websiteId   = config('services.bo.website_id');
        if ($websiteId) {
            $credentials['website_id'] = $websiteId;
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        session()->flash('gtm_event', 'login');

        // Dashboard routes live inside the {locale} prefix group, so a
        // bare `/dashboard` 404s. Prefix with the active locale, falling
        // back to the configured default if for some reason the request
        // has none resolved (direct POST to an unprefixed login URL).
        $locale = app()->getLocale() ?: config('locales.default', 'en');
        return redirect()->intended("/{$locale}/dashboard");
    }

    public function logout(Request $request)
    {
        $locale = app()->getLocale() ?: 'de';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/{$locale}");
    }
}
