<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RoomAlreadyBookedException extends Exception
{
    public function __construct($message = "Номер с указанным ID уже забронирован на указанные даты")
    {
        parent::__construct($message, 423);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $this->getMessage(),
        ], 423);
    }
}
