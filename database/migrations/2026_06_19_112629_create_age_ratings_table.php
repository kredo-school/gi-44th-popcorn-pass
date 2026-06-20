<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('age_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 50)->unique();
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('age_ratings');
    }
};