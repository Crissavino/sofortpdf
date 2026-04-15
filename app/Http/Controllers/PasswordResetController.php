<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    public function showForm()
    {
        return view('auth.password-reset');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('auth_ui.reset_err_email_required'),
            'email.email'    => __('auth_ui.reset_err_email_invalid'),
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __('auth_ui.reset_status_sent'));
        }

        return back()->withErrors([
            'email' => __('auth_ui.reset_err_user_not_found'),
        ])->onlyInput('email');
    }

    /**
     * Show the form where the user chooses a new password after clicking
     * the reset link in their email.
     */
    public function showResetConfirmForm(Request $request, string $token)
    {
        return view('auth.password-reset-confirm', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Process the new password submission.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'password.required'  => __('auth_ui.reset_err_password_required'),
            'password.confirmed' => __('auth_ui.reset_err_password_confirmed'),
            'password.min'       => __('auth_ui.reset_err_password_min', ['min' => 8]),
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __('auth_ui.reset_status_updated'));
        }

        return back()->withErrors([
            'email' => __('auth_ui.reset_err_token_invalid'),
        ])->onlyInput('email');
    }
}
