<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Conversation;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrap();

        // chat notificatio function
        View::composer('layouts.admin', function ($view) {

            $chatNotificationCount = Conversation::whereIn('status', [
                'waiting',
                'staff'
            ])->count();

            $view->with(
                'chatNotificationCount',
                $chatNotificationCount
            );
        });
    }
}
