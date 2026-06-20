<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screen_seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('screen_id');
            $table->string('seat_number', 10);
            $table->string('seat_row', 5);
            $table->integer('seat_position');
            $table->uuid('seat_category_id');
            $table->decimal('price', 8, 2);
            $table->boolean('is_wheelchair_accessible')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('seat_category_id')->references('id')->on('seat_categories');
            $table->unique(['screen_id', 'seat_number']);
            $table->index(['screen_id', 'seat_row']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screen_seats');
    }
};