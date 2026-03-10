<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedGood extends Model
{
    protected $fillable = ['product_id', 'quantity', 'type'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
