<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email'    => 'Bitte geben Sie eine gueltige E-Mail-Adresse ein.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Wir haben Ihnen einen Link zum Zuruecksetzen des Passworts per E-Mail gesendet.');
        }

        return back()->withErrors([
            'email' => 'Wir konnten keinen Benutzer mit dieser E-Mail-Adresse finden.',
        ])->onlyInput('email');
    }
}
