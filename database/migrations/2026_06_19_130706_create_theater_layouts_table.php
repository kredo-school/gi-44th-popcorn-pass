<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theater_layouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('layout_name')->unique();
            $table->text('description')->nullable();
            $table->integer('total_seats');
            $table->integer('rows');
            $table->integer('seats_per_row');
            $table->boolean('is_template')->default(true);
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theater_layouts');
    }
};