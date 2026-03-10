<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Penting untuk Transaction
use App\Models\User;
use App\Models\Shop; // Penting untuk simpan Toko

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function index()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // ===== CEK STATUS TOKO (jika bukan super_admin) =====
            if ($user->role !== 'super_admin' && $user->shop) {
                // Jika toko dibekukan/inactive, jangan izinkan login
                if ($user->shop->status === 'inactive') {
                    Auth::logout();
                    return back()->withErrors([
                        'frozen_shop' => '⚠️ Toko Anda telah dibekukan oleh admin. Hubungi support untuk informasi lebih lanjut.',
                    ]);
                }
            }

            $request->session()->regenerate();

            // Cek apakah user wajib ganti password
            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }

            // Redirect sesuai Role (menggunakan Gate/Middleware logic nanti)
            $role = $user->role;
            
            if ($role === 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($role === 'owner') {
                return redirect()->route('owner.dashboard');
            } elseif ($role === 'cashier') {
                return redirect()->route('cashier.dashboard');
            } elseif ($role === 'inventory') {
                return redirect()->route('inventory.dashboard');
            }

            return redirect('/'); 
        }

        return back()->withErrors([
            'login_error' => 'Username atau password salah.',
        ]);
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ==========================================
    // BAGIAN BARU: PENDAFTARAN (REGISTER)
    // ==========================================

    // 4. Tampilkan Form Register (Ini yang tadi error "undefined")
    public function showRegister()
    {
        // Pastikan file view ini ada di: resources/views/auth/register.blade.php
        return view('auth.register');
    }

    // 5. Proses Simpan Data Pendaftaran
    public function processRegister(Request $request)
    {
        // Validasi Input
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|unique:users,username', // Username harus unik
            'password' => 'required|min:6',
        ]);

        try {
            // Tangkap user yang dibuat supaya bisa langsung di-login setelah transaksi
            $newUser = null;

            // Gunakan Transaction agar jika salah satu gagal, semua batal disimpan
            DB::transaction(function () use ($request, &$newUser) {
                // A. Buat Tokonya
                $shop = Shop::create([
                    'name' => $request->shop_name,
                    'address' => $request->shop_address,
                    'status' => 'active', 
                    'joined_at' => now(),
                ]);

                // B. Buat Akun Owner (Link ke Toko tadi)
                $newUser = User::create([
                    'shop_id' => $shop->id, // ID dari toko yang barusan dibuat
                    'name' => $request->owner_name,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role' => 'owner',
                    'must_change_password' => false, // Owner bikin password sendiri, tidak wajib ganti
                    'is_active' => true,
                ]);
            });

            // Jika sukses, otomatis login user baru dan regenerate session
            if ($newUser) {
                Auth::login($newUser);
                $request->session()->regenerate();

                // Redirect ke dashboard owner
                return redirect()->route('owner.dashboard');
            }

            // Fallback: kembali ke login jika sesuatu aneh terjadi
            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login dengan akun Anda.');

        } catch (\Exception $e) {
            // Jika error sistem
            return back()->withErrors(['msg' => 'Gagal mendaftar: ' . $e->getMessage()]);
        }
    }
}