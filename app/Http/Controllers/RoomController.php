<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAvailableRoomsRequest;
use App\Http\Resources\AvailableRoomCollection;
use App\Models\Room;
use App\Services\RoomAvailabilityService;

class RoomController extends Controller
{
    public function getAvailableRooms(
        GetAvailableRoomsRequest $request,
        RoomAvailabilityService $roomAvailabilityService
    ): AvailableRoomCollection
    {
        $validated = $request->validated();

        $dates = $roomAvailabilityService->processInputDates(
            $validated['begin_date'] ?? null,
            $validated['end_date'] ?? null
        );

        $availableRooms = Room::availableBetween($dates['begin_date'], $dates['end_date'])->get();

        return new AvailableRoomCollection($availableRooms);
    }
}
