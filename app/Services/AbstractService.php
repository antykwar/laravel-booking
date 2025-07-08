<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class AbstractService
{
    abstract public function processInputDates(string $beginDate, string $endDate): array;

    protected function throwException(string $message, int $code)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'data' => null,
                'message' => $message,
            ], $code)
        );
    }
}
