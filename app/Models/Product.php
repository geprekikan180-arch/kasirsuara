<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Supaya kita bisa mass-input data (seperti nama, harga, stok) sekaligus
    protected $guarded = ['id'];

    // Relasi: Produk ini milik Toko siapa?
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
}