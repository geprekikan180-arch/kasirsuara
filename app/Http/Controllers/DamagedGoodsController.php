<?php

namespace App\Http\Controllers;

use App\Models\DamagedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamagedGoodsController extends Controller
{
    public function index()
    {
        $shopId = Auth::user()->shop_id;
        $damagedGoods = DamagedGood::with('product')
            ->whereHas('product', function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('owner.damaged_goods.index', compact('damagedGoods'));
    }
}
