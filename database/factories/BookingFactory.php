<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $beginDate = $this->faker->dateTimeBetween('-2 months', '+3 months');
        $endDate = (clone $beginDate)->modify('+' . rand(1, 14) . ' days');

        $beginDate->setTime(0,0);
        $endDate->setTime(23,59,59);

        return [
            'user_id' => User::factory(),
            'room_id' => Room::inRandomOrder()->first()->id,
            'begin_date' => $beginDate,
            'end_date' => $endDate,
        ];
    }
}
