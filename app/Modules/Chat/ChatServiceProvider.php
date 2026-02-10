<?php

namespace App\Modules\Chat;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
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
        $this->loadRoutesFrom(__DIR__ . '/Routes/chat.php');
    }
}
