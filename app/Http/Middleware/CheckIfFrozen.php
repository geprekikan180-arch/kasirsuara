<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfFrozen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_frozen) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dibekukan. Hubungi pemilik toko.');
        }

        return $next($request);
    }
}
