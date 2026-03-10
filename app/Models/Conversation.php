<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = ['id'];

    // Relasi ke Pesan
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relasi ke User 1
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    // Relasi ke User 2
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    // Relasi ke Toko
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}