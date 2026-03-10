<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <--- JANGAN LUPA TAMBAHKAN INI
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definisi Hak Akses (Gates)
        
        // 1. Cek Super Admin
        Gate::define('access-superadmin', function (User $user) {
            return $user->role === 'super_admin';
        });

        // 2. Cek Owner
        Gate::define('access-owner', function (User $user) {
            return $user->role === 'owner';
        });

        // 3. Cek Kasir
        Gate::define('access-cashier', function (User $user) {
            return $user->role === 'cashier';
        });

        // 4. Cek Pendata Barang (Inventory)
        Gate::define('access-inventory', function (User $user) {
            return $user->role === 'inventory';
        });
    }
}