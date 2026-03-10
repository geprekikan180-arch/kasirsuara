<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // 1. Tampilkan Form Ganti Password
    public function edit()
    {
        return view('auth.change-password');
    }

    // 2. Proses Simpan Password Baru
    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed', // Harus ada field 'password_confirmation'
        ]);

        /** @var \App\Models\User $user */  // <--- TAMBAHKAN BARIS INI
        $user = Auth::user(); // User yang sedang login (siti_kasir)

        // Update Password
        $user->password = Hash::make($request->password);
        $user->must_change_password = false; // Tandai sudah ganti password
        $user->save(); // Simpan ke database

        // Redirect User ke Dashboard masing-masing
        if ($user->role === 'cashier') {
            return redirect()->route('cashier.dashboard')->with('success', 'Password berhasil diubah!');
        } elseif ($user->role === 'inventory') {
            return redirect()->route('inventory.dashboard')->with('success', 'Password berhasil diubah!');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard')->with('success', 'Password berhasil diubah!');
        }

        return redirect('/');
    }
}