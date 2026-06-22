<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtime_seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('showtime_id');
            $table->uuid('screen_seat_id');
            $table->string('seat_status', 30)->default('available');
            $table->decimal('price_at_showtime', 8, 2);
            $table->timestamps();

            $table->foreign('showtime_id')->references('id')->on('showtimes');
            $table->foreign('screen_seat_id')->references('id')->on('screen_seats');
            $table->unique(['showtime_id', 'screen_seat_id']);
            $table->index(['showtime_id', 'seat_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtime_seats');
    }
};