<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable(false)
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('room_id')
                ->nullable(false)
                ->constrained('rooms')
                ->onDelete('cascade');

            $table->timestamp('begin_date')->nullable(false);
            $table->timestamp('end_date')->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
