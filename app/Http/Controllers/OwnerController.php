<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class OwnerController extends Controller
{
   
    
    public function dashboard()
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        
        // 1. Hitung Lama Bergabung
        $shop = Shop::find($shopId); 
        $lamaGabung = floor ($shop ? $shop->created_at->diffInDays(now()) : 0);

        // 2. Daftar Karyawan (Data Real)
        $karyawan = User::where('shop_id', $shopId)
                        ->whereIn('role', ['cashier', 'inventory', 'employee']) // Sesuaikan nama role di DB kamu
                        ->limit(5)
                        ->get(['name', 'role']);

        // 3. Stok Menipis (Data Real)
        $stokMenipis = Product::where('shop_id', $shopId)
                              ->orderBy('stock', 'asc')
                              ->limit(5)
                              ->get();

        // 4. Hitung TOTAL PENDAPATAN (Sum kolom total_amount)
        $pendapatan = Transaction::where('shop_id', $shopId)->sum('total_amount');

        // 5. Hitung JUMLAH TERJUAL (Sum quantity dari detail transaksi milik toko ini)
        $jumlahTerjual = TransactionDetail::whereHas('transaction', function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->sum('quantity');

        // 7. Hitung JUMLAH KARYAWAN
        $jumlahKaryawan = User::where('shop_id', $shopId)->count();

        // 6. Cari BARANG TERLARIS (Query Group By Product ID)
        $bestSeller = TransactionDetail::whereHas('transaction', function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })
        ->select('product_id', DB::raw('SUM(quantity) as  total_qty'))
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->with('product') // Ambil nama produknya
        ->first();

        $barangTerlaris = $bestSeller && $bestSeller->product ? $bestSeller->product->name : '-';

        // Masukkan ke array data untuk dikirim ke View
        $data = [
            'jumlah_karyawan' => $jumlahKaryawan-1,
            'jumlah_terjual' => number_format($jumlahTerjual, 0, ',', '.'),
            'barang_terlaris' => $barangTerlaris,
            'lama_gabung' => $lamaGabung,
            'pendapatan' => number_format($pendapatan, 0, ',', '.'),
            'karyawan' => $karyawan,
            'stok_menipis' => $stokMenipis
        ];

        // LOGIKA DIAGRAM: Ambil data 7 hari terakhir
        $chartDates = [];
        $chartTotals = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d'); // Format untuk query database
            $displayDate = $date->format('d M');  // Format untuk label grafik (misal: 12 Jan)

            // Hitung total pendapatan di tanggal tersebut
            $totalSales = Transaction::where('shop_id', $user->shop->id)
                ->whereDate('created_at', $dateString)
                ->sum('total_amount'); // Pastikan nama kolom harga total di tabel transaksi benar

            $chartDates[] = $displayDate;
            $chartTotals[] = $totalSales;
        }

        // Masukkan ke array $data yang sudah ada
        $data['chart_dates'] = $chartDates;
        $data['chart_totals'] = $chartTotals;

        $categories = Category::where('shop_id', $user->shop->id)->get();

        return view('owner.dashboard', compact('data', 'categories'));
    }


    public function products(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $query = Product::where('shop_id', $shopId)->with('categories');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('code', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by Category
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        } 

        // Filter by Condition
        if ($request->has('condition') && $request->condition != '') {
            $query->where('current_condition', $request->condition);
        }

        // Filter by Stock Status
        if ($request->has('stock_status') && $request->stock_status != '') {
            if ($request->stock_status == 'low') {
                $query->where('stock', '<=', 10)->where('stock', '>', 0);
            } elseif ($request->stock_status == 'out') {
                $query->where('stock', 0);
            } elseif ($request->stock_status == 'available') {
                $query->where('stock', '>', 10);
            }
        }

        $products = $query->paginate(10);
        
        // Get categories untuk dropdown filter
        $categories = Category::where('shop_id', $shopId)->pluck('name');

        // Daftar satuan yang boleh dipilih saat edit
        $units = ['pcs','kg','gram','box','meter','liter'];

        return view('owner.products', compact('products', 'categories', 'units'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;

        $product = Product::where('shop_id', $shopId)->where('id', $id)->first();
        if (!$product) {
            return back()->withErrors(['msg' => 'Produk tidak ditemukan atau Anda tidak memiliki akses.']);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|in:pcs,kg,gram,box,meter,liter',
            'categories' => 'nullable|array',
            'categories.*' => 'string',
            'current_condition' => 'nullable|in:good,damaged,expired',
        ]);

        $product->name = $validated['name'];
        if (array_key_exists('code', $validated)) $product->code = $validated['code'];
        if (array_key_exists('price', $validated)) $product->price = $validated['price'];
        if (array_key_exists('stock', $validated)) $product->stock = $validated['stock'];
        if (array_key_exists('unit', $validated)) $product->unit = $validated['unit'];
        if (array_key_exists('current_condition', $validated)) $product->current_condition = $validated['current_condition'];

        // Update categories
        if (array_key_exists('categories', $validated)) {
            $categoryIds = Category::where('shop_id', $shopId)->whereIn('name', $validated['categories'])->pluck('id');
            $product->categories()->sync($categoryIds);
        }

        $product->save();

        return back()->with('success', 'Produk berhasil diperbarui.');
    }


    public function editProfile()
{
    $user = Auth::user();
    $shop = $user->shop; // Ambil data toko dari user yang login
    return view('owner.profile', compact('user', 'shop'));
}

public function updateProfile(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $shop = $user->shop;

    $request->validate([
        'shop_name' => 'required|string|max:255',
        'logo'      => 'nullable|image|mimes:jpeg,png,jpg|max:1000', // Max 1MB
        'password'  => 'nullable|confirmed|min:8', // Password opsional (kalau diisi baru diubah)
    ]);

    // 1. Update Nama Toko
    $shop->name = $request->shop_name;

    // 2. Update Foto (Jika ada upload baru)
    if ($request->hasFile('logo')) {
    // 1. Hapus foto lama jika ada
    if ($shop->logo && Storage::disk('public')->exists('shops/' . $shop->logo)) {
        Storage::disk('public')->delete('shops/' . $shop->logo);
    }

    // 2. Simpan foto baru
    $file = $request->file('logo');
    $filename = time() . '_' . $file->getClientOriginalName();
    
    // PERBAIKAN PENTING DISINI: Tambahkan parameter 'public' di argumen ketiga
    $file->storeAs('shops', $filename, 'public'); 
    
    $shop->logo = $filename;
}

    $shop->save(); // Simpan perubahan toko

    // 3. Update Password (Jika diisi)
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
        $user->save(); // Simpan perubahan user
    }

    return back()->with('success', 'Profil toko berhasil diperbarui!');
}

    // FITUR SEARCH OWNER - Mencari Produk, Karyawan, dan Transaksi
    public function search(Request $request) 
{
    $keyword = $request->input('search');
    
    // Siapkan array kosong
    $results = [
        'products' => [],
        'users' => [],
        'transactions' => []
    ];

    if ($keyword) {
        $shopId = Auth::user()->shop_id;

        // Buat query dasar sekali dan gunakan untuk semua kondisi
        $productQuery = Product::with('categories')->where('shop_id', $shopId);

        $productQuery->where(function($q) use ($keyword) {
            // angka murni -> ID exact plus kode exact
            if (is_numeric($keyword)) {
                $q->where('id', $keyword)
                  ->orWhere('code', $keyword);
            }

            // kode bisa dicari juga dengan wildcard supaya alfanumerik ter-cover
            $q->orWhere('code', 'like', '%' . $keyword . '%');

            // nama produk mengandung
            $q->orWhere('name', 'like', '%' . $keyword . '%');

            // kategori (relasi)
            $q->orWhereHas('categories', function($q2) use ($keyword) {
                $q2->where('name', 'like', '%' . $keyword . '%');
            });

            // fallback kolom legacy bila masih ada (migrasi drop bisa menyebabkan error)
            try {
                if (
                    
                    \Illuminate\Support\Facades\Schema::hasColumn('products', 'category')
                ) {
                    $q->orWhere('category', 'like', '%' . $keyword . '%');
                }
            } catch (\Exception $e) {
                // tabel mungkin tidak ada; abaikan
            }
        });

        $results['products'] = $productQuery->get();

        // transaksi: kalau angka, cari by id
        if (is_numeric($keyword)) {
            $results['transactions'] = Transaction::where('id', $keyword)->get();
        }

        // pengguna tetap mencari nama/username
        if (!is_numeric($keyword)) {
            $results['users'] = User::where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('username', 'like', '%' . $keyword . '%')
                                    ->get();
        }
    }

    return view('owner.search', [
        'searchQuery' => $keyword,
        'results' => $results
    ]);
    }

    // Category management
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,shop_id,' . Auth::user()->shop_id,
        ]);

        Category::create([
            'shop_id' => Auth::user()->shop_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroyCategory($id)
    {
        $category = Category::where('shop_id', Auth::user()->shop_id)->findOrFail($id);
        
        // Check if category is used by products
        if ($category->products()->exists()) {
            return back()->withErrors(['msg' => 'Kategori tidak bisa dihapus karena masih digunakan oleh produk.']);
        }
        
        $category->delete();
        
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}