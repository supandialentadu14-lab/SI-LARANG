<?php

namespace App\Providers;

// Import model User untuk digunakan di Gate
use App\Models\User;

// Facade Gate untuk membuat authorization (hak akses)
use Illuminate\Support\Facades\Gate;

// Class dasar ServiceProvider
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * Digunakan untuk binding service ke container.
     * Biasanya dipakai untuk dependency injection.
     * 
     * Saat ini belum digunakan.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * Method ini akan dijalankan saat aplikasi pertama kali di-load.
     * Biasanya digunakan untuk:
     * - Authorization (Gate / Policy)
     * - View Composer
     * - Global configuration
     */
    public function boot(): void
    {
        // Set locale Carbon ke Bahasa Indonesia
        \Carbon\Carbon::setLocale('id');

        /**
         * ==========================================
         * 🔐 GATE: ADMIN ACCESS
         * ==========================================
         * 
         * Mendefinisikan hak akses bernama 'admin-access'.
         * 
         * Cara pakai di controller / blade:
         * 
         * Gate::allows('admin-access')
         * atau
         * @can('admin-access')
         */
        Gate::define('admin-access', function (User $user) {
            return $user->role === 'admin';
        });


        /**
         * ==========================================
         * 📦 VIEW COMPOSER: layouts.admin
         * ==========================================
         * 
         * View Composer akan otomatis menjalankan kode ini
         * setiap kali view 'layouts.admin' dipanggil.
         * 
         * Tujuannya:
         * Mengirim data produk dengan stok rendah ke layout admin.
         */
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {

            // Cek dulu apakah tabel products sudah ada
            // Ini mencegah error saat pertama kali migrate
            if (\Illuminate\Support\Facades\Schema::hasTable('products')) {

                // Ambil produk yang stoknya <= min_stock
                $lowStockProducts = \App\Models\Product::whereColumn(
                    'stock',
                    '<=',
                    'min_stock'
                )
                ->take(5) // Batasi hanya 5 produk
                ->get();

                // Kirim data ke view dengan nama variable:
                // $lowStockProducts
                $view->with('lowStockProducts', $lowStockProducts);
            }
        });
    }
}
