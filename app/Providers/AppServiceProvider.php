<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\Room;
use App\Exceptions\RoomNotFoundException;

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
        Route::bind('room', function ($value) {
            $room = Room::find($value);

            if (!$room) {
                throw new RoomNotFoundException();
            }

            return $room;
        });
    }
}
