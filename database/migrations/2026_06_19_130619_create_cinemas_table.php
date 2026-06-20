<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('cinema_name');
            $table->string('city', 100);
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('total_screens');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_id')->references('id')->on('users');
            $table->index('city');
            $table->index('cinema_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
};