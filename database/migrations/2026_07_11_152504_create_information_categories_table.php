<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('information_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('color', 20)->default('#6C757D');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_categories');
    }
};