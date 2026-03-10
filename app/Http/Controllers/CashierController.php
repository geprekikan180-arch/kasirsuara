<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- Penting untuk keamanan data

class CashierController extends Controller
{
    public function dashboard()
    {
        $shopId = Auth::user()->shop_id;
        $products = Product::where('shop_id', $shopId)
                           ->where('stock', '>', 0)
                           ->with('categories')
                           ->get();

        return view('cashier.dashboard', compact('products'));
    }

    // 👇 INI FUNGSI BARU: PROSES TRANSAKSI
    public function processTransaction(Request $request)
    {
        // 1. Terima data dari Javascript (Keranjang belanja)
        $cart = $request->input('cart'); // Isinya array barang
        $totalPrice = $request->input('total_price');
        $cashPaid = $request->input('cash_paid'); // Uang yang dibayar
        
        if (!$cart || count($cart) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Keranjang kosong!'], 400);
        }

        // 2. Mulai Database Transaction (Biar aman, kalau error 1, batal semua)
        DB::beginTransaction();

        try {

            // A. Buat Struk Baru (Header)
            // Hitung kembalian sekarang (kita simpan di DB)
            $change = null;
            if (is_numeric($cashPaid)) {
                $change = (int) $cashPaid - (int) $totalPrice;
            }

            // Sesuaikan field yang ada di migration: payment_method, transaction_date, cash_paid, change
            $transaction = Transaction::create([
                'shop_id' => Auth::user()->shop_id,
                'user_id' => Auth::id(),
                'transaction_code' => 'TRX-' . time() . '-' . rand(100, 999), // Contoh: TRX-170000-123
                'total_amount' => $totalPrice,
                'payment_method' => $request->input('payment_method', 'cash'),
                'cash_paid' => is_numeric($cashPaid) ? (int) $cashPaid : null,
                'change' => $change,
                'transaction_date' => now(),
            ]);

            // B. Masukkan Rincian Barang & POTONG STOK
            foreach ($cart as $item) {
                // Ambil data produk asli di database untuk cek stok terbaru
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product || $product->stock < $item['qty']) {
                    // Kalau tiba-tiba stok habis saat mau bayar
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Stok ' . $item['name'] . ' tidak cukup!'], 400);
                }

                // Simpan detail (termasuk subtotal sesuai migration)
                $subtotal = intval($item['qty']) * intval($item['price']);
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price_at_transaction' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // KURANGI STOK
                $product->stock = $product->stock - $item['qty'];
                $product->save();
            }

            // C. Kalau semua lancar, Simpan Permanen!
            DB::commit();

            // Return response termasuk kembalian jika ada
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi Berhasil!',
                'transaction_code' => $transaction->transaction_code,
                'change' => $transaction->change,
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua kalau ada error
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    // FITUR SEARCH CASHIER - Mencari Produk dan Transaksi
    public function search(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $searchQuery = $request->get('search', '');

        $results = [
            'products' => [],
            'transactions' => [],
        ];

        if (!empty($searchQuery)) {
            // Cari Produk (untuk penjualan)
            $results['products'] = Product::where('shop_id', $shopId)
                ->where('stock', '>', 0)
                ->where(function($q) use ($searchQuery) {
                    // exact id or code when query is numeric
                    if (is_numeric($searchQuery)) {
                        $q->where('id', $searchQuery)
                          ->orWhere('code', $searchQuery);
                    }

                    // nama / partial match
                    $q->orWhere('name', 'like', "%{$searchQuery}%");

                    // kategori melalui relasi
                    $q->orWhereHas('categories', function($q2) use ($searchQuery) {
                        $q2->where('name', 'like', "%{$searchQuery}%");
                    });

                    // legacy column bila masih ada
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'category')) {
                            $q->orWhere('category', 'like', "%{$searchQuery}%");
                        }
                    } catch (\Exception $e) {
                        // abaikan
                    }
                })
                ->limit(10)
                ->get();

            // Cari Transaksi (riwayat penjualan)
            $results['transactions'] = Transaction::where('shop_id', $shopId)
                ->where('user_id', Auth::id())
                ->where(function($q) use ($searchQuery) {
                    $q->where('id', 'like', "%{$searchQuery}%");
                })
                ->limit(10)
                ->get();
        }

        return view('cashier.search', compact('results', 'searchQuery'));
    }

    // 👇 API BARU: Get Kategori yang tersedia
    public function getCategories()
    {
        $shopId = Auth::user()->shop_id;

        // pilih lima kategori dengan produk terbanyak di toko ini
        $categories = \App\Models\Category::where('shop_id', $shopId)
            ->withCount(['products' => function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            }])
            ->orderByDesc('products_count')
            ->limit(5)
            ->pluck('name');

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    // 👇 API BARU: Live Search Produk (nama, kode, kategori)
    public function searchProducts(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $query = $request->get('q', '');
        $category = $request->get('category', null); // Filter kategori opsional

        $products = Product::where('shop_id', $shopId)
                          ->where('stock', '>', 0);

        // Filter berdasarkan kategori jika ada
        if ($category && $category !== 'semua') {
            // periksa relasi categories terlebih dahulu, gunakan OR untuk kolom string legacy
            $products = $products->where(function($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('categories', function($q2) use ($category) {
                      $q2->where('name', $category);
                  });
            });
        }

        // Jika ada query pencarian, cari di nama, kode, dan kategori
        if (!empty($query)) {
            $products = $products->where(function($q) use ($query) {
                // numeric -> exact id/code
                if (is_numeric($query)) {
                    $q->where('id', $query)
                      ->orWhere('code', $query);
                }

                // wildcard code & nama
                $q->orWhere('code', 'like', "%{$query}%");
                $q->orWhere('name', 'like', "%{$query}%");
                $q->orWhereHas('categories', function($q2) use ($query) {
                    $q2->where('name', 'like', "%{$query}%");
                });

                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'category')) {
                        $q->orWhere('category', 'like', "%{$query}%");
                    }
                } catch (\Exception $e) {
                    // ignore
                }
            });
        }

        $products = $products->get();

        return response()->json([
            'status' => 'success',
            'products' => $products
        ]);
    }
}