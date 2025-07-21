<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ticket;
use App\Observers\TicketObserver;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;


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
        Ticket::observe(TicketObserver::class);
        FilamentAsset::register([
            Js::make('notification-sound', public_path('js/notification-sound.js')),
        ]);


    }
}
