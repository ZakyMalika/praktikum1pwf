<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
        // 3. Definisikan Gate 'export-product' di sini
        Gate::define('export-product', function (User $user) {
            // Logika: Hanya return true (diizinkan) jika role user adalah 'admin'
            return $user->role === 'admin';
        });
    }
}
