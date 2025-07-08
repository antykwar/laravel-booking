<?php

namespace App\Services;

use App\Exceptions\RoomAlreadyBookedException;
use App\Exceptions\RoomInBookingProgressException;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Room;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingRoomService extends AbstractService
{
    public function bookRoom(Room $room, Authenticatable $user, string $beginDate, string $endDate): void
    {
        DB::beginTransaction();
        try {
            $room = Room::where('id', $room->id)->lockForUpdate()->first();
            if (!$room) {
                throw new RoomInBookingProgressException();
            }

            [$beginDate, $endDate] = array_values($this->processInputDates($beginDate, $endDate));
            if (!$room->isAvailable($beginDate, $endDate)) {
                throw new RoomAlreadyBookedException();
            }

            $booking = new Booking();
            $booking->room()->associate($room);
            $booking->user()->associate($user);
            $booking->fill(['begin_date' => $beginDate, 'end_date' => $endDate]);
            $booking->save();

            Mail::to($user)->send(new BookingConfirmed($booking));

            DB::commit();
        } catch (RoomInBookingProgressException|RoomAlreadyBookedException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (Exception $exception) {
            DB::rollBack();
            $this->throwException($exception->getMessage(), 500);
        }
    }

    public function processInputDates(string $beginDate, string $endDate): array
    {
        return [
            'begin_date' => Carbon::parse($beginDate)
                ->setTime(0, 0)
                ->format('Y-m-d H:i:s'),
            'end_date' => Carbon::parse($endDate)
                ->setTime(23, 59, 59)
                ->format('Y-m-d H:i:s'),
        ];
    }
}
