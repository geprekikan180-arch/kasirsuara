<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillTransactionCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update transaksi lama: jika cash_paid null, anggap dibayar sesuai total_amount dan change = 0
        $updated = DB::table('transactions')
            ->whereNull('cash_paid')
            ->update([
                'cash_paid' => DB::raw('total_amount'),
                'change' => 0,
            ]);

        $this->command->info("BackfillTransactionCashSeeder: updated {$updated} transactions.");
    }
}
