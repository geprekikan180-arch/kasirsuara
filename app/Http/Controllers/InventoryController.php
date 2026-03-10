<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\DamagedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    // 1. Tampilkan Dashboard Gudang (Form Input & Tabel Stok)
    public function index()
    {
        $shopId = Auth::user()->shop_id;
        $query = Product::where('shop_id', $shopId)->with('categories');

        // Filter by Category
        if (request()->has('category') && request()->category != '') {
            $query->whereHas('categories', function($q) {
                $q->where('name', request()->category);
            });
        }

        // Filter by Condition
        if (request()->has('condition') && request()->condition != '') {
            $query->where('current_condition', request()->condition);
        }

        // Filter by Stock Status
        if (request()->has('stock_status') && request()->stock_status != '') {
            if (request()->stock_status == 'low') {
                $query->where('stock', '<=', 10)->where('stock', '>', 0);
            } elseif (request()->stock_status == 'out') {
                $query->where('stock', 0);
            } elseif (request()->stock_status == 'available') {
                $query->where('stock', '>', 10);
            }
        }

        $products = $query->orderBy('updated_at', 'desc')->get();
        
        // Get categories untuk dropdown filter
        $categories = Category::where('shop_id', $shopId)->pluck('name')->sort()->values();

        return view('inventory.dashboard', compact('products', 'categories'));
    }

    // 2. Proses Input Barang (LOGIKA PINTAR)
    // 2. Proses Input Barang (SUDAH DIPERBAIKI: Barcode -> Code)
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string', // Di form namanya tetap 'barcode'
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:1',
            'unit' => 'required|string|in:pcs,kg,gram,box,meter,liter',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string',
        ]);

        $shopId = Auth::user()->shop_id;

        // Check if all categories exist for this shop
        $existingCategories = Category::where('shop_id', $shopId)->whereIn('name', $request->categories)->pluck('name')->toArray();
        $missingCategories = array_diff($request->categories, $existingCategories);
        if (!empty($missingCategories)) {
            return redirect()->back()->withErrors(['categories' => 'Kategori berikut tidak ditemukan: ' . implode(', ', $missingCategories)]);
        }

        $shopId = Auth::user()->shop_id;

        // --- LOGIKA PENCEGAH TABRAKAN ---
        // Kita cari berdasarkan kolom 'code' (bukan barcode)
        $existingProduct = Product::where('shop_id', $shopId)
                                  ->where('code', $request->barcode) // <--- UBAH DISINI
                                  ->first();

        if ($existingProduct) {
            // jika nama berbeda dan user berusaha menambahkan barang baru, kita tolak
            if (strtolower(trim($existingProduct->name)) !== strtolower(trim($request->name))) {
                return redirect()->back()->withErrors(['barcode' => "Kode '{$request->barcode}' sudah digunakan untuk barang '{$existingProduct->name}'. Periksa kembali nama atau gunakan kode lain."]);
            }
            // SKENARIO A: BARANG SUDAH ADA -> Tambah Stok
            $existingProduct->stock = $existingProduct->stock + $request->stock;

            // Update info lain (termasuk unit jika user pilih berbeda)
            $existingProduct->price = $request->price;
            $existingProduct->name = $request->name;
            $existingProduct->unit = $request->unit ?? $existingProduct->unit;

            $existingProduct->save();

            // Update categories
            $categoryIds = Category::where('shop_id', $shopId)->whereIn('name', $request->categories)->pluck('id');
            $existingProduct->categories()->sync($categoryIds);

            return redirect()->back()->with('success', "Stok '{$existingProduct->name}' bertambah! Total: {$existingProduct->stock}");
        } else {
            // SKENARIO B: BARANG BARU -> Buat Baru
            $product = Product::create([
                'shop_id' => $shopId,
                'code'    => $request->barcode, // <--- UBAH DISINI: Isi kolom 'code' dengan data 'barcode' dari form
                'name'    => $request->name,
                'price'   => $request->price,
                'stock'   => $request->stock,
                'unit'    => $request->unit ?? 'pcs',
                'image'   => 'https://via.placeholder.com/150?text='.urlencode($request->name),
            ]);

            // Attach categories
            $categoryIds = Category::where('shop_id', $shopId)->whereIn('name', $request->categories)->pluck('id');
            $product->categories()->attach($categoryIds);

            return redirect()->back()->with('success', 'Produk BARU berhasil disimpan.');
        }
    }

    // Tambahkan method ini di dalam class InventoryController

    public function update(Request $request, $id)
    {
        $product = \App\Models\Product::where('shop_id', Auth::user()->shop_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            // Code/Barcode biasanya tidak boleh diganti sembarangan
        ]);

        return redirect()->back()->with('success', 'Data barang diperbarui.');
    }

    public function destroy($id)
    {
        $product = \App\Models\Product::where('shop_id', Auth::user()->shop_id)->findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Barang dihapus dari gudang.');
    }


    public function conditionIndex()
{
    $user = Auth::user();
    // Ambil semua barang milik toko ini
    $products = Product::where('shop_id', $user->shop_id)->with('categories')->get();
    
    return view('inventory.condition', compact('products'));
}

public function updateCondition(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'condition'  => 'required|in:good,damaged,expired',
        'quantity'   => 'required_if:condition,damaged,expired|integer|min:1',
    ]);

    $product = Product::find($request->product_id);

    if (in_array($request->condition, ['damaged', 'expired'])) {
        // Validasi quantity tidak boleh lebih dari stok
        if ($request->quantity > $product->stock) {
            return response()->json(['error' => 'Jumlah rusak/basi tidak boleh lebih dari stok tersedia.'], 400);
        }

        // Kurangi stok
        $product->stock -= $request->quantity;
        $product->save();

        // Simpan ke damaged_goods
        DamagedGood::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'type' => $request->condition,
        ]);
    } else {
        // Jika good, set kondisi saja
        $product->current_condition = $request->condition;
        $product->save();
    }

    return response()->json(['message' => 'Kondisi berhasil diupdate!']);
}

    // API: Cari produk berdasarkan kode/barcode (dipakai oleh form input)
    public function find(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $shopId = Auth::user()->shop_id;

        $product = Product::where('shop_id', $shopId)
                          ->where('code', $request->code)
                          ->first();

        if (!$product) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'categories' => $product->categories->pluck('name'),
            'price' => $product->price,
            'code' => $product->code,
            'stock' => $product->stock,
            'current_condition' => $product->current_condition,
        ]);
    }

    // FITUR SEARCH INVENTORY - Mencari Barang di Gudang
    public function search(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $searchQuery = $request->get('search', '');

        $results = [
            'products' => [],
        ];

        if (!empty($searchQuery)) {
            // Cari Barang di gudang
            $results['products'] = Product::with('categories')->where('shop_id', $shopId)
                ->where(function($q) use ($searchQuery) {
                    // angka -> id / code exact
                    if (is_numeric($searchQuery)) {
                        $q->where('id', $searchQuery)
                          ->orWhere('code', $searchQuery);
                    }

                    // wildcard kode serta nama
                    $q->orWhere('code', 'like', "%{$searchQuery}%");
                    $q->orWhere('name', 'like', "%{$searchQuery}%");

                    // nama kategori di relasi
                    $q->orWhereHas('categories', function($q2) use ($searchQuery) {
                        $q2->where('name', 'like', "%{$searchQuery}%");
                    });

                    // legacy column
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'category')) {
                            $q->orWhere('category', 'like', "%{$searchQuery}%");
                        }
                    } catch (\Exception $e) {
                        // ignoring if schema not accessible
                    }
                })
                ->limit(10)
                ->get();
        }

        return view('inventory.search', compact('results', 'searchQuery'));
    }

    // Tambahkan ini di dalam class InventoryController

    // ---------------------------------------------------------
    // API KHUSUS VALIDASI SUARA (Untuk Poin 8)
    // ---------------------------------------------------------
    public function validateVoiceInput(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $shopId = Auth::user()->shop_id;
        
        // Cari apakah kode sudah ada?
        $existingProduct = Product::where('shop_id', $shopId)
                                  ->where('code', $request->code)
                                  ->first();

        if (!$existingProduct) {
            // Skenario: Barang Benar-benar Baru
            return response()->json(['status' => 'new']);
        }

        // Skenario: Kode Ada. Cek Namanya.
        // Kita gunakan similar_text atau strpos untuk toleransi sedikit typo dari suara
        // Tapi untuk keamanan data, exact match (case insensitive) lebih baik.
        
        // Bersihkan nama dari spasi/huruf kecil untuk perbandingan
        $inputName = strtolower(trim($request->name));
        $dbName = strtolower(trim($existingProduct->name));

        if ($inputName && $inputName !== $dbName) {
            // Poin 8: Kode sama, tapi nama beda -> ERROR
            return response()->json([
                'status' => 'conflict',
                'message' => "Bahaya! Kode '{$request->code}' sudah dipakai oleh barang lain: '{$existingProduct->name}'. Ganti kode atau ucapkan nama barang dengan benar."
            ]);
        }

        // Poin 8: Kode sama, nama sama (atau user cuma sebut kode untuk nambah stok)
        return response()->json([
            'status' => 'exists',
            'product' => $existingProduct,
            'message' => "Barang ditemukan. Stok akan ditambahkan ke '{$existingProduct->name}'."
        ]);
    }

    // API: Get all categories untuk dropdown
    public function getCategories()
    {
        $shopId = Auth::user()->shop_id;
        
        $categories = Category::where('shop_id', $shopId)->pluck('name')->sort()->values();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }
}
