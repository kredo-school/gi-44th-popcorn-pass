<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');
            $table->uuid('showtime_seat_id');
            $table->decimal('price_at_reservation', 8, 2);
            $table->timestamp('created_at')->nullable();

            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->foreign('showtime_seat_id')->references('id')->on('showtime_seats');
            $table->index('reservation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_seats');
    }
};