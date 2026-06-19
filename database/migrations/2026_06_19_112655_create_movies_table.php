<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('genre_id');
            $table->integer('duration');
            $table->text('synopsis')->nullable();
            $table->string('director', 100)->nullable();
            $table->uuid('age_rating_id')->nullable();
            $table->date('released_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('poster_url', 500)->nullable();
            $table->string('banner_image_url', 500)->nullable();
            $table->string('trailer_url', 500)->nullable();
            $table->json('cast')->nullable();
            $table->string('status', 30)->default('coming_soon');
            $table->boolean('is_featured')->default(false);
            $table->integer('priority_order')->default(999);
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('box_office', 12, 2)->nullable();
            $table->decimal('review_average', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('popularity_score')->default(0);
            $table->json('search_keywords')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->foreign('genre_id')->references('id')->on('genres');
            $table->foreign('age_rating_id')->references('id')->on('age_ratings');
            $table->foreign('created_by_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};