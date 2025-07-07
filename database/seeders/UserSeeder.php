<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Harry',
            'email' => 'harry@hogwarts.magic',
            'password' => Hash::make('Harry'),
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Hermione',
            'email' => 'hermione@hogwarts.magic',
            'password' => Hash::make('Hermione'),
            'email_verified_at' => now(),
        ]);
    }
}
