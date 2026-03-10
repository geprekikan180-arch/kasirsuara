<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = \App\Models\Shop::all();
        $categories = ['Makanan', 'Minuman', 'Snack', 'Bahan Pokok', 'Kesehatan', 'Kosmetik'];

        foreach ($shops as $shop) {
            foreach ($categories as $category) {
                \App\Models\Category::firstOrCreate([
                    'shop_id' => $shop->id,
                    'name' => $category,
                ]);
            }
        }
    }
}
