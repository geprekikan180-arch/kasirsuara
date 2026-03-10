<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function index()
    {
        // 1. DATA RINGKASAN
        $totalShops = Shop::count();
        $newShopsCount = Shop::where('created_at', '>=', now()->subDays(30))->count();
        $totalUsers = User::whereIn('role', ['owner', 'cashier', 'inventory'])->count();

        // 2. PESAN MASUK
        $userId = Auth::id();
        $unreadMessages = Message::where('is_read', false)
            ->where('sender_id', '!=', $userId)
            // Hapus type hint 'Builder' agar tidak merah di VS Code
            ->whereHas('conversation', function($q) use ($userId) {
                $q->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
            })->count();

        // 3. TABEL TOKO TERBARU
        $latestShops = Shop::latest()->limit(10)->get();

        // 4. CHART
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->format('F');
            $chartData[] = Shop::whereYear('created_at', $date->format('Y'))
                         ->whereMonth('created_at', $date->month)
                         ->count();
        }

        // DEBUGGING: Uncomment baris di bawah ini jika error masih muncul
        // dd($newShopsCount); 

        // Mengirim data menggunakan Array explisit agar lebih aman
        return view('superadmin.dashboard', [
            'totalShops' => $totalShops,
            'newShopsCount' => $newShopsCount, // <--- Ini yang dicari View
            'totalUsers' => $totalUsers,
            'unreadMessages' => $unreadMessages,
            'latestShops' => $latestShops,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ]);
    }
}