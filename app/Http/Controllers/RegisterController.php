<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showForm()
    {
        return redirect()->route('home');
    }

    public function register(Request $request)
    {
        return redirect()->route('home');
    }
}
