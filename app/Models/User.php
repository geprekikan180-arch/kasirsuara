<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'shop_id',
        'name',
        'username',
        'password',
        'role',
        'must_change_password',
        'is_active',
        'is_frozen',
    ];

    /**
     * Kolom yang disembunyikan saat return data (keamanan).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data otomatis.
     */
    protected $casts = [
        'password' => 'hashed',
        'must_change_password' => 'boolean',
        'is_active' => 'boolean',
        'is_frozen' => 'boolean',
    ];

    // Relasi: User milik 1 Toko (kecuali Super Admin)
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function hasRole($roleName)
    {
        // Cek apakah kolom role isinya sama dengan parameter
        return $this->role === $roleName;
    }
}