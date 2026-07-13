<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDynamicPricingToShowtimes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->decimal('base_price', 8, 2)->default(1900.00)->comment('Original ticket price');
            $table->decimal('elasticity_factor', 3, 2)->default(0.50)->comment('Price sensitivity to occupancy (0.0-2.0)');
            $table->decimal('current_dynamic_price', 8, 2)->default(1900.00)->comment('Current calculated price');
            $table->decimal('occupancy_rate', 3, 2)->default(0.00)->comment('Current occupancy percentage (0.0-1.0)');
            $table->integer('capacity')->default(0)->comment('Total seats in this screen');
            $table->integer('booked_seats')->default(0)->comment('Currently booked seats');
            $table->timestamp('last_price_update')->nullable()->comment('When price was last recalculated');
        });

        // Add index for performance
        Schema::table('showtimes', function (Blueprint $table) {
            $table->index(['screen_id', 'movie_id', 'start_time', 'last_price_update'], 'idx_dynamic_pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropIndex('idx_dynamic_pricing');
            $table->dropColumn([
                'base_price',
                'elasticity_factor',
                'current_dynamic_price',
                'occupancy_rate',
                'capacity',
                'booked_seats',
                'last_price_update'
            ]);
        });
    }
}