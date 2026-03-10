<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class TransactionController extends Controller
{
    // Halaman Riwayat Transaksi (Bisa dipakai Owner & Kasir)
    public function history(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $query = Transaction::where('shop_id', $shopId)
                    ->with(['user', 'details']); // Load relasi user & detail barang

        // Range tanggal (jika ada start_date & end_date)
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->input('start_date', null);
            $end   = $request->input('end_date', null);
            if ($start && $end) {
                if ($start > $end) {
                    // swap agar tidak kacau
                    [$start, $end] = [$end, $start];
                }
                $query->whereDate('created_at', '>=', $start)
                      ->whereDate('created_at', '<=', $end);
            } elseif ($start) {
                $query->whereDate('created_at', '>=', $start);
            } elseif ($end) {
                $query->whereDate('created_at', '<=', $end);
            }
        } else {
            // fallback: single date if provided (compatibilitas lama)
            if ($request->has('date') && $request->date != '') {
                $query->whereDate('created_at', $request->date);
            }
        }

        // Filter berdasarkan User (Kasir) - untuk owner
        if ($request->has('user_id') && $request->user_id != '' && Auth::user()->role === 'owner') {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan Range Total
        if ($request->has('min_amount') && $request->min_amount != '') {
            $query->where('total_amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount != '') {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // Urutkan dari terbaru
        $transactions = $query->latest()->paginate(10);
        
        // Get users untuk dropdown filter (hanya untuk owner)
        $users = Auth::user()->role === 'owner' 
            ? User::where('shop_id', $shopId)->whereIn('role', ['cashier', 'inventory'])->get()
            : collect([]);

        return view('transactions.history', compact('transactions', 'users'));
    }


    // Fitur Laporan Bulanan (ubah menjadi rentang tanggal)
    public function monthlyReport1(Request $request)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;

        // Input preferensi: start_date & end_date (format: YYYY-MM-DD)
        // Default: awal dan akhir bulan ini
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        // Jika user salah input sehingga start > end, swap agar aman
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Query Rekap Harian (Group By Date) berdasarkan rentang tanggal
        $laporanHarian = Transaction::where('shop_id', $shopId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->select(
                DB::raw('DATE(transaction_date) as date'), // Ambil tanggal saja
                DB::raw('COUNT(*) as total_transaksi'),    // Hitung jumlah transaksi
                DB::raw('SUM(total_amount) as total_pendapatan') // Hitung total uang
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Ambil detail transaksi per hari untuk dropdown/accordion
        $detailTransaksiPerHari = [];
        foreach ($laporanHarian as $row) {
            $detailTransaksiPerHari[$row->date] = Transaction::where('shop_id', $shopId)
                ->whereDate('transaction_date', $row->date)
                ->with(['user', 'details.product'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Hitung total untuk rentang ini
        $totalPendapatanBulanIni = $laporanHarian->sum('total_pendapatan');
        $totalTransaksiBulanIni = $laporanHarian->sum('total_transaksi');

        return view('owner.report', compact(
            'laporanHarian',
            'detailTransaksiPerHari',
            'totalPendapatanBulanIni', 
            'totalTransaksiBulanIni',
            'startDate',
            'endDate'
        ));
    }
}