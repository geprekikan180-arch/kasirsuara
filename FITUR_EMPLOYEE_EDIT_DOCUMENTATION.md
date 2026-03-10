# Dokumentasi Perubahan Fitur Karyawan

## Ringkasan Perubahan
Fitur hapus karyawan telah diganti dengan fitur edit karyawan, ditambah dengan status karyawan yang dapat dibekukan (frozen).

## Alasan Perubahan
Karyawan yang sudah ada tidak dapat dihapus karena memiliki hubungan dengan data transaksi. Oleh karena itu, fitur hapus diganti dengan fitur edit untuk memungkinkan pengelolaan yang lebih fleksibel, termasuk kemampuan untuk membekukan akun karyawan sehingga mereka tidak dapat login.

---

## Detail Perubahan

### 1. **Model User** (`app/Models/User.php`)
- Ditambahkan field `is_frozen` ke dalam `$fillable` array
- Ditambahkan casting boolean untuk `is_frozen` di `$casts` array

### 2. **Migration** (Database)
File baru: `database/migrations/2026_01_27_000000_add_is_frozen_to_users_table.php`
- Menambahkan kolom boolean `is_frozen` ke tabel `users` dengan default value `false`

### 3. **Controller Employee** (`app/Http/Controllers/EmployeeController.php`)
- **Method `store()`**: Ditambahkan `'is_frozen' => false` saat membuat karyawan baru
- **Method `update()` (BARU)**: Menangani update data karyawan
  - Dapat mengubah nama lengkap
  - Dapat mengubah role (cashier/inventory)
  - Dapat mengubah status karyawan (aktif/dibekukan)
  - Username tidak dapat diubah untuk menjaga integritas data

### 4. **Route** (`routes/web.php`)
- Ditambahkan route PUT untuk update karyawan:
  ```
  Route::put('/toko/karyawan/{id}', [EmployeeController::class, 'update'])->name('employees.update');
  ```

### 5. **View Employee Index** (`resources/views/owner/employees/index.blade.php`)
Perubahan signifikan:
- **Tabel**: Ditambahkan kolom baru "Status Karyawan" dengan opsi:
  - ✓ Aktif (hijau)
  - ✗ Dibekukan (merah)
- **Aksi**: Tombol hapus (delete) diganti dengan tombol edit (edit)
- **Modal Edit (BARU)**: Menampilkan form edit dengan field:
  - Nama Lengkap
  - Username (disabled/read-only)
  - Posisi/Role (Kasir/Gudang)
  - Status Karyawan (Aktif/Dibekukan)
- **JavaScript Function**: `openEditModal()` untuk membuka modal edit dengan data karyawan

### 6. **Middleware ForcePasswordChange** (`app/Http/Middleware/ForcePasswordChange.php`)
- Ditambahkan pengecekan `is_frozen` di awal middleware
- Jika karyawan dibekukan, akan di-logout otomatis dengan pesan error:
  ```
  "Akun Anda telah dibekukan. Hubungi pemilik toko."
  ```

### 7. **Middleware CheckIfFrozen** (BARU - Optional)
File: `app/Http/Middleware/CheckIfFrozen.php`
- Middleware tambahan untuk pengecekan apakah akun dibekukan
- Bisa digunakan untuk route tertentu jika diperlukan pengawasan lebih ketat

---

## Fitur Hasil Perubahan

### Untuk Owner/Pemilik Toko:
1. ✓ Melihat daftar karyawan dengan status akun dan status karyawan
2. ✓ Menambah karyawan baru
3. ✓ **Edit** data karyawan (nama, role, status)
4. ✓ Membekukan akun karyawan tanpa menghapusnya dari database
5. ✓ Mengaktifkan kembali karyawan yang sebelumnya dibekukan

### Untuk Karyawan (Kasir/Inventory):
1. ✓ Dapat login jika akun aktif (is_frozen = false)
2. ✗ Tidak dapat login jika akun dibekukan (is_frozen = true)
3. ✗ Akan otomatis di-logout jika akun dibekukan saat sedang login

---

## Cara Menggunakan

### Untuk Mengedit Karyawan:
1. Buka halaman "Manajemen Karyawan" (`/toko/karyawan`)
2. Klik tombol **edit** (ikon pensil) pada baris karyawan yang ingin diedit
3. Modal edit akan terbuka dengan data karyawan terkini
4. Ubah data yang diperlukan (nama, role, status)
5. Klik "Simpan Perubahan"

### Untuk Membekukan Karyawan:
1. Klik tombol **edit** pada karyawan
2. Di bagian "Status Karyawan", pilih opsi **"Bekukan"**
3. Klik "Simpan Perubahan"
4. Karyawan tidak akan bisa login lagi sampai status diaktifkan kembali

---

## Catatan Penting

1. **Migration Belum Dijalankan**: Jangan lupa menjalankan `php artisan migrate` untuk membuat kolom `is_frozen` di database
2. **Username Read-Only**: Username karyawan tidak dapat diubah dalam fitur edit untuk menjaga konsistensi data
3. **Soft Delete**: Karyawan tidak akan pernah terhapus dari database, hanya bisa dibekukan atau diaktifkan
4. **Integritas Data**: Semua transaksi yang terkait dengan karyawan tetap aman karena tidak ada penghapusan data

---

## Testing Checklist

- [ ] Jalankan migration untuk menambah kolom `is_frozen`
- [ ] Test tambah karyawan baru
- [ ] Test edit nama karyawan
- [ ] Test ubah role karyawan
- [ ] Test bekukan akun karyawan
- [ ] Verifikasi karyawan yang dibekukan tidak bisa login
- [ ] Test aktifkan kembali karyawan yang dibekukan
- [ ] Verifikasi karyawan aktif dapat login kembali

---

## File yang Dimodifikasi
1. `app/Models/User.php` - Model User
2. `app/Http/Controllers/EmployeeController.php` - Controller
3. `routes/web.php` - Route
4. `resources/views/owner/employees/index.blade.php` - View
5. `app/Http/Middleware/ForcePasswordChange.php` - Middleware

## File yang Ditambahkan
1. `database/migrations/2026_01_27_000000_add_is_frozen_to_users_table.php` - Migration
2. `app/Http/Middleware/CheckIfFrozen.php` - Optional Middleware
