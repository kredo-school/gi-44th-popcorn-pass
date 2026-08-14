<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('screen_id');
            $table->uuid('movie_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unique(['screen_id', 'start_time']);
            $table->index(['movie_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};