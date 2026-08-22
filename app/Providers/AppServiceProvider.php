<?php

namespace App\Providers;

use App\Models\Cinema;
use App\Models\Conversation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | Selected Cinema
        |--------------------------------------------------------------------------
        |
        | Get the cinema selected by the user from the session and make it
        | available to all Blade views as $selectedCinema.
        |
        | Example:
        | POPCORN PASS - Osaka
        |
        */

        View::composer('*', function ($view) {
            static $resolved = false;
            static $selectedCinema = null;

            if (! $resolved) {
                $selectedCinemaId = session('selected_cinema_id');

                if ($selectedCinemaId) {
                    $selectedCinema = Cinema::where('is_active', true)
                        ->find($selectedCinemaId);

                    // Remove an invalid or deleted cinema from the session
                    if (! $selectedCinema) {
                        session()->forget('selected_cinema_id');
                    }
                }

                $resolved = true;
            }

            $view->with('selectedCinema', $selectedCinema);
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Chat Notification
        |--------------------------------------------------------------------------
        */

        View::composer('layouts.admin', function ($view) {
            $chatNotificationCount = Conversation::whereIn('status', [
                'waiting',
                'staff',
            ])->count();

            $view->with(
                'chatNotificationCount',
                $chatNotificationCount
            );
        });
    }
}
