<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layout_seat_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('layout_id');
            $table->string('seat_number', 10);
            $table->uuid('seat_category_id');
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_wheelchair_accessible')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->foreign('layout_id')->references('id')->on('theater_layouts');
            $table->foreign('seat_category_id')->references('id')->on('seat_categories');
            $table->unique(['layout_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layout_seat_assignments');
    }
};