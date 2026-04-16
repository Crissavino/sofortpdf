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
            'email.required'    => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email'       => 'Bitte geben Sie eine gueltige E-Mail-Adresse ein.',
            'password.required' => 'Bitte geben Sie Ihr Passwort ein.',
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
                'email' => 'Diese Anmeldedaten stimmen nicht mit unseren Aufzeichnungen überein.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
