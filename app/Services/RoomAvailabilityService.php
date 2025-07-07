<?php

namespace App\Services;

use Carbon\Carbon;

class RoomAvailabilityService
{
    public function processInputDates(?string $beginDate, ?string $endDate): array
    {
        return [
            'begin_date' => $beginDate ?: Carbon::today()
                ->setTime(0, 0)
                ->format('Y-m-d'),
            'end_date' => $endDate ?: Carbon::today()
                ->addDays(6)
                ->setTime(23, 23, 59)
                ->format('Y-m-d'),
        ];
    }
}
