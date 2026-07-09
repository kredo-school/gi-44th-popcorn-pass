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
        Schema::create('information', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->text('content');
            $table->string('category')->default('General');
            $table->string('status')->default('Draft');
            $table->timestamp('published_at')->nullable();

            $table->uuid('created_by_id')->nullable();
            $table->foreign('created_by_id')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information');
    }
};
