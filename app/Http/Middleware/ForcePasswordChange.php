<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Cek apakah akun user dibekukan
        if ($user && $user->is_frozen) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dibekukan. Hubungi pemilik toko.');
        }

        // Jika user login DAN status must_change_password = true
        // DAN user tidak sedang berada di halaman ganti password atau proses ganti password
        if ($user && $user->must_change_password) {
            if (!$request->routeIs('password.change') && !$request->routeIs('password.update')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}