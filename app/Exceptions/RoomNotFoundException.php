<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RoomNotFoundException extends Exception
{
    public function __construct($message = "Номер с указанным ID не найден")
    {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $this->getMessage(),
            'errors' => null,
        ], 404);
    }
}
