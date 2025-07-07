<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Получение списка доступных номеров
    Route::get('/rooms/available', [RoomController::class, 'getAvailableRooms']);

    // Бронирование номера
    Route::post('/rooms/{room}/book', [RoomController::class, 'bookRoom'])
        ->middleware('auth:sanctum');
});
