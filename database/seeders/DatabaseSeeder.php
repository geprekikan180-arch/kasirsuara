<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat 1 Super Admin
        User::create([
            'name' => 'Labibul Humam',
            'username' => 'superadmin',
            'password' => bcrypt('password123'), // Password awal
            'role' => 'super_admin',
            'shop_id' => null,
            'must_change_password' => false, // Admin tidak perlu ganti password dulu
            'is_active' => true,
        ]);

        // Buat Toko Sample
        $shop = Shop::create([
            'name' => 'Toko Maju Jaya',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Buat Owner untuk Toko
        User::create([
            'shop_id' => $shop->id,
            'name' => 'Budi Santoso',
            'username' => 'owner',
            'password' => bcrypt('password123'),
            'role' => 'owner',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        // Buat Sample Products
        $products = [
            ['code' => 'PRD001', 'name' => 'Kecap Bango 50ml', 'category' => 'Makanan', 'price' => 5000, 'stock' => 15, 'unit' => 'pcs'],
            ['code' => 'PRD002', 'name' => 'Minyak Goreng 2L', 'category' => 'Makanan', 'price' => 35000, 'stock' => 8, 'unit' => 'botol'],
            ['code' => 'PRD003', 'name' => 'Beras Putih 5kg', 'category' => 'Makanan', 'price' => 65000, 'stock' => 20, 'unit' => 'karung'],
            ['code' => 'PRD004', 'name' => 'Gula Pasir 1kg', 'category' => 'Makanan', 'price' => 15000, 'stock' => 3, 'unit' => 'kemasan'],
            ['code' => 'PRD005', 'name' => 'Telur Ayam 1kg', 'category' => 'Daging', 'price' => 28000, 'stock' => 0, 'unit' => 'kg'],
            ['code' => 'PRD006', 'name' => 'Daging Ayam 1kg', 'category' => 'Daging', 'price' => 45000, 'stock' => 12, 'unit' => 'kg'],
            ['code' => 'PRD007', 'name' => 'Daging Sapi 1kg', 'category' => 'Daging', 'price' => 85000, 'stock' => 5, 'unit' => 'kg'],
            ['code' => 'PRD008', 'name' => 'Roti Tawar', 'category' => 'Makanan', 'price' => 20000, 'stock' => 25, 'unit' => 'pack'],
            ['code' => 'PRD009', 'name' => 'Susu Cair 1L', 'category' => 'Minuman', 'price' => 18000, 'stock' => 30, 'unit' => 'botol'],
            ['code' => 'PRD010', 'name' => 'Teh Botol 330ml', 'category' => 'Minuman', 'price' => 8000, 'stock' => 50, 'unit' => 'botol'],
        ];

        foreach ($products as $product) {
            Product::create([
                'shop_id' => $shop->id,
                ...$product
            ]);
        }

        // Backfill transaksi lama untuk kolom cash_paid & change jika perlu
        $this->call(\Database\Seeders\BackfillTransactionCashSeeder::class);
    }
}
