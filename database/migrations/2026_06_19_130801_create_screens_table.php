<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cinema_id');
            $table->integer('screen_number');
            $table->string('screen_name', 100)->nullable();
            $table->string('screen_type', 50)->nullable();
            $table->uuid('layout_id');
            $table->integer('total_seats');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('cinema_id')->references('id')->on('cinemas');
            $table->foreign('layout_id')->references('id')->on('theater_layouts');
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unique(['cinema_id', 'screen_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};