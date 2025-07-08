<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RoomInBookingProgressException extends Exception
{
    public function __construct($message = "Номер не доступен для бронирования в текущий момент, попробуйте позже")
    {
        parent::__construct($message, 409);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $this->getMessage(),
        ], 409);
    }
}
