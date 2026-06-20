<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 50);
            $table->decimal('discount_value', 10, 2);
            $table->uuid('applicable_genre_id')->nullable();
            $table->uuid('applicable_movie_id')->nullable();
            $table->uuid('applicable_seat_category_id')->nullable();
            $table->uuid('applicable_cinema_id')->nullable();
            $table->string('applicable_screen_type', 50)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);
            $table->integer('min_ticket_purchase')->default(1);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('promotion_status', 30)->default('active');
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('applicable_genre_id')->references('id')->on('genres');
            $table->foreign('applicable_movie_id')->references('id')->on('movies');
            $table->foreign('applicable_seat_category_id')->references('id')->on('seat_categories');
            $table->foreign('applicable_cinema_id')->references('id')->on('cinemas');
            $table->foreign('created_by_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};