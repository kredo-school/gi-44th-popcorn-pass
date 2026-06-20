<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('showtime_id');
            $table->uuid('screen_id');
            $table->uuid('cinema_id');
            $table->uuid('movie_id');
            $table->string('reservation_status', 30)->default('pending');
            $table->string('qr_code', 500)->nullable();
            $table->integer('total_seats')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->string('reservation_reference', 50)->unique()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('showtime_id')->references('id')->on('showtimes');
            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('cinema_id')->references('id')->on('cinemas');
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->index(['user_id', 'created_at']);
            $table->index('reservation_status');
            $table->index('cinema_id');
            $table->index('screen_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};