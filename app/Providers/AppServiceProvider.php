<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // // Tambahkan kode ini:
        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        // Backward compatibility gate
        Gate::define('role=Administrator', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('role=Admin', function ($user) {
            return $user->isAdmin();
        });

        // Gates Hak Akses Divisi (Administrator otomatis memiliki akses ke semua divisi)
        Gate::define('access-admin', fn($user) => $user->isAdmin());
        Gate::define('access-iklan', fn($user) => $user->canAccessDivision('iklan'));
        Gate::define('access-keuangan', fn($user) => $user->canAccessDivision('keuangan'));
        Gate::define('access-accounting', fn($user) => $user->canAccessDivision('accounting'));
        Gate::define('access-sirkulasi', fn($user) => $user->canAccessDivision('sirkulasi'));
        Gate::define('access-kasir', fn($user) => $user->canAccessDivision('kasir'));
    }
}
