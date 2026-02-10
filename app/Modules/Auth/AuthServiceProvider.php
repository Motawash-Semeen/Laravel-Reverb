<?php

namespace App\Modules\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any module services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/auth.php');
    }
}
