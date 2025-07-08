<?php

namespace App\Http\Controllers;

use App\Services\BookingRoomService;
use App\Http\Requests\BookRoomRequest;
use App\Http\Requests\GetAvailableRoomsRequest;
use App\Http\Resources\AvailableRoomCollection;
use App\Models\Room;
use App\Services\RoomAvailabilityService;

class RoomController extends Controller
{
    public function getAvailableRooms(
        GetAvailableRoomsRequest $request,
        RoomAvailabilityService $roomAvailabilityService
    ): AvailableRoomCollection {
        $validated = $request->validated();

        $dates = $roomAvailabilityService->processInputDates(
            $validated['begin_date'] ?? null,
            $validated['end_date'] ?? null
        );

        $availableRooms = Room::availableBetween($dates['begin_date'], $dates['end_date'])
            ->orderBy('id')
            ->get();

        return new AvailableRoomCollection($availableRooms);
    }

    public function bookRoom(
        BookRoomRequest $request,
        BookingRoomService $bookingRoomService,
        Room $room
    ) {
        $validated = $request->validated();

        $bookingRoomService->bookRoom(
            $room,
            auth()->user(),
            $validated['begin_date'],
            $validated['end_date']
        );


    }
}
