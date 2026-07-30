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
        // Create cinema_reviews table
        Schema::create('cinema_reviews', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('cinema_id', 36)->index();
            $table->char('user_id', 36)->index();

            // Rating dimensions (1.0 - 5.0)
            $table->decimal('image_quality', 2, 1)->comment('Screen resolution, brightness, color accuracy');
            $table->decimal('sound_quality', 2, 1)->comment('Dolby Atmos, surround, bass quality');
            $table->decimal('seat_comfort', 2, 1)->comment('Reclining, cushioning, width');
            $table->decimal('crowding_level', 2, 1)->comment('Occupancy, toilet wait time (lower is better)');
            $table->decimal('accessibility', 2, 1)->comment('Parking, public transit, wheelchair access');
            $table->decimal('service_quality', 2, 1)->comment('Staff, food quality, WiFi');

            // Optional comment
            $table->text('comment')->nullable();

            // Metadata
            $table->integer('review_count')->default(1)->comment('Number of visits this user counted');
            $table->date('visited_at')->comment('When user last visited this cinema');

            $table->timestamps();

            // Foreign keys
            $table->foreign('cinema_id')->references('id')->on('cinemas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['cinema_id', 'user_id']);
            $table->unique(['user_id', 'cinema_id', 'visited_at']);
        });

        // Add columns to cinemas table
        Schema::table('cinemas', function (Blueprint $table) {
            // Check if columns don't already exist
            if (!Schema::hasColumn('cinemas', 'avg_image_quality')) {
                $table->decimal('avg_image_quality', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_sound_quality')) {
                $table->decimal('avg_sound_quality', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_seat_comfort')) {
                $table->decimal('avg_seat_comfort', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_crowding_level')) {
                $table->decimal('avg_crowding_level', 2, 1)->default(3.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_accessibility')) {
                $table->decimal('avg_accessibility', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_service_quality')) {
                $table->decimal('avg_service_quality', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'avg_experience_score')) {
                $table->decimal('avg_experience_score', 2, 1)->default(4.0);
            }
            if (!Schema::hasColumn('cinemas', 'total_reviews')) {
                $table->integer('total_reviews')->default(0);
            }
            if (!Schema::hasColumn('cinemas', 'last_score_update')) {
                $table->timestamp('last_score_update')->nullable();
            }

            // Index for sorting by score
            $table->index('avg_experience_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cinemas', function (Blueprint $table) {
            $table->dropColumn([
                'avg_image_quality',
                'avg_sound_quality',
                'avg_seat_comfort',
                'avg_crowding_level',
                'avg_accessibility',
                'avg_service_quality',
                'avg_experience_score',
                'total_reviews',
                'last_score_update',
            ]);
        });

        Schema::dropIfExists('cinema_reviews');
    }
};