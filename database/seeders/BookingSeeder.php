<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $bookingCount = 5;
        $maxAttempts = 20;

        $users = User::whereIn('email', ['harry@hogwarts.magic', 'hermione@hogwarts.magic'])->get();
        $rooms = Room::all();

        // Для каждого пользователя создаём не более $bookingCount бронирований
        // Каждую сгенерированную запись проверяем на пересечение с имеющимися бронированиями для номера
        // Делаем максимум $maxAttempts попыток генерации бронирований на пользователя

        foreach ($users as $user) {
            $createdBookings = 0;
            $attempts = 0;

            while ($createdBookings < $bookingCount && $attempts < $maxAttempts) {
                $attempts++;

                // Часть броней создаём в прошлом
                $isPast = $createdBookings < 2;
                $startDate = $isPast ? '-2 months' : '+1 month';

                $room = $rooms->random();

                $beginDate = Carbon::now()
                    ->modify($startDate)
                    ->addDays(rand(0, 30))
                    ->setTime(0,0);
                $endDate = (clone $beginDate)
                    ->addDays(rand(1, 14))
                    ->setTime(23,59,59);

                // Ищем бронирования, которые пересекаются с создаваемым для конкретного номера
                $hasOverlap = Booking::where('room_id', $room->id)
                    ->where(function($query) use ($beginDate, $endDate) {
                        $query->whereBetween('begin_date', [$beginDate, $endDate])
                            ->orWhereBetween('end_date', [$beginDate, $endDate])
                            ->orWhere(function($q) use ($beginDate, $endDate) {
                                $q->where('begin_date', '<', $beginDate)
                                    ->where('end_date', '>', $endDate);
                            });
                    })
                    ->exists();

                // Если пересечения не найдены - добавляем запись о бронировании
                if (!$hasOverlap) {
                    Booking::create([
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'begin_date' => $beginDate,
                        'end_date' => $endDate,
                    ]);
                    $createdBookings++;
                }
            }
        }
    }
}
