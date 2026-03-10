<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // 1. Tampilkan Daftar Karyawan
    public function index()
    {
         $query = User::where('shop_id', Auth::user()->shop_id);

        // Ambil ID toko milik user yang sedang login (Owner)
        $shopId = Auth::user()->shop_id;

        // Ambil semua user di toko ini, KECUALI si Owner sendiri
        // Kita mau ambil role 'cashier' dan 'inventory'
        $employees = User::where('shop_id', $shopId)
                         ->whereIn('role', ['cashier', 'inventory'])
                         ->orderBy('created_at', 'desc')
                         ->get();

        
        $employees = $query->paginate(7);
        
        return view('owner.employees.index', compact('employees'));
    }

    // 2. Simpan Karyawan Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|unique:users,username',
            'role' => 'required|in:cashier,inventory', // Hanya boleh pilih Kasir atau Gudang
        ]);

        // Buat Akun Karyawan
        User::create([
            'shop_id' => Auth::user()->shop_id, // Masuk ke toko si Owner
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make('123456'), // Password default
            'role' => $request->role,
            'must_change_password' => true, // WAJIB Ganti Password nanti
            'is_active' => true,
            'is_frozen' => false,
        ]);

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan! Password default: 123456');
    }
    

    // 3. Update Karyawan
    public function update(Request $request, $id)
    {
        $employee = User::where('shop_id', Auth::user()->shop_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:cashier,inventory',
            'is_frozen' => 'required|boolean',
        ]);

        $employee->update([
            'name' => $request->name,
            'role' => $request->role,
            'is_frozen' => $request->is_frozen,
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    // 4. Hapus Karyawan (Misal resign)
    public function destroy($id)
    {
        $employee = User::where('shop_id', Auth::user()->shop_id)->findOrFail($id);
        $employee->delete();

        return redirect()->back()->with('success', 'Data karyawan dihapus.');
    }
}