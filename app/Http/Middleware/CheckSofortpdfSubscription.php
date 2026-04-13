<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSofortpdfSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            $returnUrl = $request->url();
            return redirect()->route('checkout.start', ['return_to' => $returnUrl]);
        }

        if (!$user->hasSofortpdfSubscription()) {
            $returnUrl = $request->url();
            return redirect()->route('checkout.start', ['return_to' => $returnUrl]);
        }

        return $next($request);
    }
}
