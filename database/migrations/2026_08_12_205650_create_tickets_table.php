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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('reservation_seat_id')->unique();

            $table->string('qr_token', 128)->unique();

            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->foreign('reservation_seat_id')
                ->references('id')
                ->on('reservation_seats')
                ->cascadeOnDelete();

            $table->index('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};