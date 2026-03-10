# Dokumentasi Fitur Search & Filter - Kasir Suara Pintar

## 📋 Ringkasan Perubahan

Telah ditambahkan fitur search per-role dan filter data untuk meningkatkan pengalaman pengguna dalam mencari dan mengelompokkan data.

---

## 🔍 FITUR SEARCH PER-ROLE

### 1. **Owner** (`/toko/search`)
Dapat mencari:
- **Barang**: Nama, Kode, Kategori
- **Karyawan**: Nama (Cashier, Inventory, Employee)
- **Transaksi**: Kode Transaksi, Nama Kasir

**File Modified:**
- Controller: [app/Http/Controllers/OwnerController.php](app/Http/Controllers/OwnerController.php)
- View: [resources/views/owner/search.blade.php](resources/views/owner/search.blade.php)
- Route: `Route::get('/toko/search', [OwnerController::class, 'search'])->name('owner.search');`

---

### 2. **Cashier** (`/kasir/search`)
Dapat mencari:
- **Produk**: Nama, Kode (dengan stok > 0)
- **Transaksi Pribadi**: Kode Transaksi (hanya transaksi milik kasir yang login)

**File Modified:**
- Controller: [app/Http/Controllers/CashierController.php](app/Http/Controllers/CashierController.php)
- View: [resources/views/cashier/search.blade.php](resources/views/cashier/search.blade.php)
- Route: `Route::get('/kasir/search', [CashierController::class, 'search'])->name('cashier.search');`

---

### 3. **Inventory** (`/gudang/search`)
Dapat mencari:
- **Barang**: Nama, Kode, Kategori (dalam satu gudang/toko)

**File Modified:**
- Controller: [app/Http/Controllers/InventoryController.php](app/Http/Controllers/InventoryController.php)
- View: [resources/views/inventory/search.blade.php](resources/views/inventory/search.blade.php)
- Route: `Route::get('/gudang/search', [InventoryController::class, 'search'])->name('inventory.search');`

---

## 🎯 FITUR FILTER

### 1. **Filter Barang (Inventory Dashboard)** 
**Lokasi:** [resources/views/inventory/dashboard.blade.php](resources/views/inventory/dashboard.blade.php)

Filter yang tersedia:
- **Kategori**: Dropdown list kategori barang yang tersedia
- **Status Stok**: 
  - Tersedia (>10)
  - Hampir Habis (1-10)
  - Habis (0)
- **Kondisi Barang**:
  - Baik
  - Rusak
  - Basi/Expired

**Fitur:**
- Tombol Filter untuk menerapkan filter
- Tombol Reset (X) untuk menghapus semua filter
- Filter bekerja secara realtime dengan kombinasi parameter

---

### 2. **Filter Transaksi (History Transaksi)** 
**Lokasi:** [resources/views/transactions/history.blade.php](resources/views/transactions/history.blade.php)

Filter yang tersedia:
- **Tanggal**: Input date picker untuk filter spesifik satu hari
- **Bulan**: Dropdown bulan (1-12)
- **Tahun**: Dropdown tahun (5 tahun ke belakang)
- **Kasir** (Owner only): Dropdown untuk filter by kasir/user tertentu

**Fitur:**
- Tombol Filter untuk menerapkan filter
- Tombol Reset untuk menghapus semua filter
- Kombinasi filter dapat digunakan bersama-sama
- Untuk Cashier: hanya bisa melihat transaksi mereka sendiri

---

## 🔐 KEAMANAN - ROLE SEPARATION

### Search Panel Dinamis
File: [resources/views/components/search-panel.blade.php](resources/views/components/search-panel.blade.php)

Fitur:
- Search form otomatis mengarahkan ke route yang benar sesuai role
- Placeholder berbeda untuk setiap role
- Tidak ada tabrakan antar role - query dijaga untuk hanya search data toko sendiri

### Middleware Protection
- Setiap route dilindungi dengan middleware `can:access-{role}`
- Owner tidak bisa melihat data cashier/inventory lain
- Cashier hanya bisa search produk dan transaksi mereka sendiri
- Inventory staff terisolasi ke gudang mereka

---

## 🛠️ IMPLEMENTASI TEKNIS

### Database Query dengan Shop Isolation
```php
// Semua query menggunakan shop_id user yang login
$query = Model::where('shop_id', Auth::user()->shop_id)
```

### Dynamic Route untuk Search
```php
// Controller method
public function search(Request $request) { ... }

// Route
Route::get('/path/search', [Controller::class, 'search'])->name('{role}.search');
```

---

## 📝 CATATAN PENGGUNAAN

### Bagi Owner:
1. Gunakan search untuk mencari barang, karyawan, atau transaksi dengan cepat
2. Gunakan filter di halaman Riwayat untuk analisis penjualan per periode
3. Filter barang bisa dikombinasikan untuk analisis inventory

### Bagi Cashier:
1. Gunakan search untuk mencari produk saat melayani pelanggan
2. Riwayat transaksi mereka tersimpan otomatis dan dapat dicari

### Bagi Inventory:
1. Gunakan search dan filter untuk manajemen stok yang efisien
2. Filter status stok membantu identifikasi barang yang perlu reorder

---

## 🔄 FLOW PENGGUNAAN

1. **User Login** → Role ditentukan oleh `Auth::user()->role`
2. **Search Panel Muncul** → Form search menunjukkan placeholder sesuai role
3. **User Mengetik Keyword** → Form submit ke route yang sesuai role
4. **Controller Filter Query** → Hanya menampilkan data dari toko user + role-specific filters
5. **Hasil Ditampilkan** → View yang sesuai dengan role

---

## ✅ Testing Checklist

- [ ] Owner bisa search barang, karyawan, transaksi
- [ ] Cashier hanya bisa search produk dan transaksinya sendiri
- [ ] Inventory hanya bisa search barang di gudangnya
- [ ] Filter kategori bekerja di inventory dashboard
- [ ] Filter status stok bekerja dengan benar
- [ ] Filter kondisi barang bekerja
- [ ] Filter bulan/tahun/kasir bekerja di riwayat transaksi
- [ ] Reset filter berfungsi dengan baik
- [ ] Cross-role tabrakan tidak terjadi

---

## 📦 Files Modified/Created

### Modified:
1. [resources/views/components/search-panel.blade.php](resources/views/components/search-panel.blade.php)
2. [app/Http/Controllers/OwnerController.php](app/Http/Controllers/OwnerController.php)
3. [app/Http/Controllers/CashierController.php](app/Http/Controllers/CashierController.php)
4. [app/Http/Controllers/InventoryController.php](app/Http/Controllers/InventoryController.php)
5. [app/Http/Controllers/TransactionController.php](app/Http/Controllers/TransactionController.php)
6. [resources/views/inventory/dashboard.blade.php](resources/views/inventory/dashboard.blade.php)
7. [resources/views/transactions/history.blade.php](resources/views/transactions/history.blade.php)
8. [routes/web.php](routes/web.php)

### Created:
1. [resources/views/owner/search.blade.php](resources/views/owner/search.blade.php)
2. [resources/views/cashier/search.blade.php](resources/views/cashier/search.blade.php)
3. [resources/views/inventory/search.blade.php](resources/views/inventory/search.blade.php)

---

## 🎨 UI/UX Details

- **Search Panel**: Placeholder dinamis, form action ke route yang benar
- **Search Results**: Grid layout untuk barang, table untuk karyawan, cards untuk transaksi
- **Filter UI**: Dropdown untuk kategori, status, kondisi; date picker untuk tanggal
- **Empty States**: Pesan friendly ketika tidak ada hasil pencarian atau filter kosong
- **Responsive**: Semua view responsive untuk mobile dan desktop

