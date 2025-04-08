<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bỏ comment để cho phép kênh private và presence hoạt động
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
