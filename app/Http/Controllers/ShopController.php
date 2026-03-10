<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // 1. Tampilkan Daftar Toko
    public function index()
    {
        // Ambil data toko terbaru dulu (descending)
        $shops = Shop::orderBy('created_at', 'desc')->get();
        
        return view('superadmin.shops.index', compact('shops'));
    }

    // 2. Simpan Toko Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        // Generate nama unik untuk folder/kode jika perlu nanti
        // Untuk sekarang simpan standar saja
        Shop::create([
            'name' => $request->name,
            'address' => $request->address,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Toko berhasil didaftarkan!');
    }

    // 3. Update Status (Aktif/Nonaktif)
    public function toggleStatus($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->status = ($shop->status == 'active') ? 'inactive' : 'active';
        $shop->save();

        return redirect()->back()->with('success', 'Status toko diperbarui.');
    }

    // 4. Tampilkan Detail Toko
    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        
        // Ambil karyawan toko (paginate 5 per halaman)
        $employees = $shop->users()
            ->where('role', '!=', 'owner')
            ->paginate(10);

        // Ambil data untuk grafik pertumbuhan (6 bulan terakhir)
        $chartLabels = [];
        $transactionCounts = [];
        $revenueCounts = [];
        $productCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $chartLabels[] = $date->format('M');

            // Jumlah Transaksi
            $transactionCounts[] = \App\Models\Transaction::where('shop_id', $id)
                ->whereYear('created_at', $date->format('Y'))
                ->whereMonth('created_at', $date->month)
                ->count();

            // Total Omset (Revenue)
            $revenueCounts[] = \App\Models\Transaction::where('shop_id', $id)
                ->whereYear('created_at', $date->format('Y'))
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');

            // Jumlah Produk
            $productCounts[] = \App\Models\Product::where('shop_id', $id)
                ->whereYear('created_at', $date->format('Y'))
                ->whereMonth('created_at', $date->month)
                ->count();

            
        }

        return view('superadmin.shops.show', compact(
            'shop',
            'employees',
            'chartLabels',
            'transactionCounts',
            'revenueCounts',
            'productCounts'
        ));
    }
}