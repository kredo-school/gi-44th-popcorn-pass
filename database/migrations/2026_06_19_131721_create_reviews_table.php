<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('movie_id');
            $table->uuid('user_id');
            $table->integer('rating');
            $table->text('body')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_moderated')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->uuid('moderated_by_id')->nullable();
            $table->string('moderation_reason')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->integer('unhelpful_count')->default(0);
            $table->timestamps();

            $table->foreign('movie_id')->references('id')->on('movies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('moderated_by_id')->references('id')->on('users');
            $table->index('movie_id');
            $table->index('user_id');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};