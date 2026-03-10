<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DamagedGoodsController;
use App\Http\Controllers\TransactionController; // <--- Tambahan Baru
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SuperAdminController;

// Halaman Awal
Route::get('/', function () {
    return view('welcome');
});


Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');

// Middleware Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'processRegister'])->name('register.process');
});

// Authenticated
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/{conversation}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::get('/chat/api/conversations', [ChatController::class, 'getConversations'])->name('chat.api.conversations');
    
    // Route khusus untuk memulai chat baru dari modal kontak
    Route::get('/chat/start/{user}', [ChatController::class, 'checkOrCreateRoom'])->name('chat.start');
    Route::get('/chat/{conversation}/fetch', [ChatController::class, 'fetchNewMessages'])->name('chat.fetch');
    Route::get('/chat/{conversation}/api', [ChatController::class, 'detail'])->name('chat.api.detail');

    // Ganti Password
    Route::get('/ganti-password', [PasswordController::class, 'edit'])->name('password.change');
    Route::post('/ganti-password', [PasswordController::class, 'update'])->name('password.update');

    // Grouping Force Password Change
    Route::middleware(['force.change.password'])->group(function () {
        
        // 1. Super Admin
        Route::middleware('can:access-superadmin')->group(function () {
            
            // PERBAIKAN DISINI: Sekarang mengarah ke Controller, bukan function biasa
            Route::get('/admin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');

            Route::get('/admin/shops', [ShopController::class, 'index'])->name('shops.index');
            Route::get('/admin/shops/{id}', [ShopController::class, 'show'])->name('shops.show');
            Route::post('/admin/shops', [ShopController::class, 'store'])->name('shops.store');
            Route::patch('/admin/shops/{id}/toggle', [ShopController::class, 'toggleStatus'])->name('shops.toggle');
        });

        // 2. Owner Toko
        Route::middleware('can:access-owner')->group(function () {
            Route::get('/toko/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
            
            // Manajemen Produk
            Route::get('/toko/data-barang', [OwnerController::class, 'products'])->name('owner.products');
            // Update produk (inline edit dari halaman owner)
            Route::put('/toko/data-barang/{id}', [OwnerController::class, 'updateProduct'])->name('owner.products.update');
            
            // Manajemen Karyawan
            Route::get('/toko/karyawan', [EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/toko/karyawan', [EmployeeController::class, 'store'])->name('employees.store');
            Route::put('/toko/karyawan/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/toko/karyawan/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

            // Barang Rusak
            Route::get('/toko/barang-rusak', [DamagedGoodsController::class, 'index'])->name('damaged_goods.index');

            // --- TAMBAHAN: Owner bisa lihat Riwayat & Laporan ---
            Route::get('/toko/riwayat', [TransactionController::class, 'history'])->name('owner.transactions');
            Route::get('/toko/lapora', [TransactionController::class, 'monthlyReport1'])->name('owner.reports');
            Route::get('/toko/laporan', [TransactionController::class, 'monthlyReport1'])->name('owner.report');

            // FITUR EDIT PROFIL TOKO
            Route::get('/toko/profil', [OwnerController::class, 'editProfile'])->name('owner.profile');
            Route::put('/toko/profil', [OwnerController::class, 'updateProfile'])->name('owner.profile.update');

            // FITUR SEARCH OWNER
            Route::get('/toko/search', [OwnerController::class, 'search'])->name('owner.search');

            // Category management
            Route::post('/toko/kategori', [OwnerController::class, 'storeCategory'])->name('owner.categories.store');
            Route::delete('/toko/kategori/{id}', [OwnerController::class, 'destroyCategory'])->name('owner.categories.destroy');
        });

        // 3. Kasir
        Route::middleware('can:access-cashier')->group(function () {
            Route::get('/kasir/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
            Route::post('/kasir/bayar', [CashierController::class, 'processTransaction'])->name('cashier.process');
            
            // --- TAMBAHAN: Kasir juga butuh lihat riwayat hari ini ---
            Route::get('/kasir/riwayat', [TransactionController::class, 'history'])->name('cashier.transactions');

            // FITUR SEARCH CASHIER
            Route::get('/kasir/search', [CashierController::class, 'search'])->name('cashier.search');

            // 👇 API BARU: Get Kategori & Live Search Produk
            Route::get('/kasir/api/categories', [CashierController::class, 'getCategories'])->name('cashier.api.categories');
            Route::get('/kasir/api/search-products', [CashierController::class, 'searchProducts'])->name('cashier.api.search');
        });

        // 4. Gudang (Inventory)
        Route::middleware('can:access-inventory')->group(function () {
            Route::get('/gudang/dashboard', [InventoryController::class, 'index'])->name('inventory.dashboard');
            Route::post('/gudang/tambah-barang', [InventoryController::class, 'store'])->name('inventory.store');
            
            // --- TAMBAHAN: Update & Delete Barang ---
            Route::put('/gudang/barang/{id}', [InventoryController::class, 'update'])->name('inventory.update');
            Route::delete('/gudang/barang/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

            Route::get('/gudang/kondisi', [InventoryController::class, 'conditionIndex'])->name('inventory.condition');
            Route::post('/gudang/kondisi/update', [InventoryController::class, 'updateCondition'])->name('inventory.condition.update');

            // FITUR SEARCH INVENTORY
            Route::get('/gudang/search', [InventoryController::class, 'search'])->name('inventory.search');
            Route::get('/gudang/product/find', [InventoryController::class, 'find'])->name('inventory.find');
            Route::get('/gudang/api/categories', [InventoryController::class, 'getCategories'])->name('inventory.api.categories');

            Route::post('/inventory/validate-voice', [InventoryController::class, 'validateVoiceInput']);
        });
    });
});