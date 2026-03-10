<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'status', 'joined_at'];

    // Relasi: Toko punya banyak User (Owner, Kasir, Pendata)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi: Toko punya banyak Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    
}