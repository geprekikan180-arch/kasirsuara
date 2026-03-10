<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Kita pakai guarded kosong agar semua kolom (shop_id, user_id, dll) bisa diisi
    protected $guarded = [];

    // Cast numeric money fields to integers
    protected $casts = [
        'total_amount' => 'integer',
        'cash_paid' => 'integer',
        'change' => 'integer',
    ];

    // Relasi: Transaksi dimiliki oleh 1 User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Transaksi punya banyak detail barang
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi: Transaksi milik toko
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}