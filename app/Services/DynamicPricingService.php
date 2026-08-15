<?php

namespace App\Services;

use App\Models\Showtime;
use App\Models\ReservationSeat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DynamicPricingService
{
    /**
     * Calculate occupancy rate for a showtime (0.0 - 1.0)
     */
    public function calculateOccupancyRate($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);

        // If capacity is 0, return 0
        if ($showtime->capacity <= 0) {
            return 0.0;
        }

        // Count booked seats: COUNT(DISTINCT showtime_seat_id) where reservation_seats exist for this showtime
        $bookedSeats = ReservationSeat::query()
            ->whereNull('cancelled_at')
            ->whereHas(
                'showtimeSeat',
                function ($query) use ($showtimeId) {
                    $query->where(
                        'showtime_id',
                        $showtimeId
                    );
                }
            )
            ->distinct('showtime_seat_id')
            ->count('showtime_seat_id');

        // Calculate occupancy rate
        $occupancyRate = $bookedSeats / $showtime->capacity;

        // Clamp between 0.0 and 1.0
        return max(0.0, min(1.0, $occupancyRate));
    }

    /**
     * Calculate dynamic price using formula:
     * current_dynamic_price = base_price × (1 + (occupancy_rate × elasticity_factor))
     */
    public function calculateDynamicPrice($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);

        // Calculate occupancy rate
        $occupancyRate = $this->calculateOccupancyRate($showtimeId);

        // Apply formula
        $multiplier = 1 + ($occupancyRate * $showtime->elasticity_factor);
        $dynamicPrice = $showtime->base_price * $multiplier;

        // Apply min/max multiplier bounds from config
        $minMultiplier = config('dynamic_pricing.min_multiplier', 0.85);
        $maxMultiplier = config('dynamic_pricing.max_multiplier', 1.50);

        $minPrice = $showtime->base_price * $minMultiplier;
        $maxPrice = $showtime->base_price * $maxMultiplier;

        $dynamicPrice = max($minPrice, min($maxPrice, $dynamicPrice));

        return round($dynamicPrice, 2);
    }

    /**
     * Update dynamic price and occupancy for a showtime in the database
     */
    public function updateDynamicPrice($showtimeId)
    {
        try {
            $showtime = Showtime::findOrFail($showtimeId);

            // Skip if showtime is in the past
            if ($showtime->start_time < now()) {
                return false;
            }

            $occupancyRate = $this->calculateOccupancyRate($showtimeId);
            $newPrice = $this->calculateDynamicPrice($showtimeId);
            $oldPrice = $showtime->current_dynamic_price;

            // Update showtime
            $showtime->update([
                'occupancy_rate' => $occupancyRate,
                'current_dynamic_price' => $newPrice,
                'last_price_update' => now(),
            ]);

            // Log price change if enabled
            if (config('dynamic_pricing.log_price_changes', true) && $newPrice !== $oldPrice) {
                Log::channel('dynamic_pricing')->info('Price updated', [
                    'showtime_id' => $showtimeId,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'occupancy_rate' => $occupancyRate,
                    'price_change_percent' => round((($newPrice - $oldPrice) / $oldPrice) * 100, 2),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update dynamic price', [
                'showtime_id' => $showtimeId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get formatted price string (e.g., "¥2,042.50")
     */
    public function getFormattedPrice($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);
        return '¥' . number_format($showtime->current_dynamic_price, 2);
    }

    /**
     * Get price indicator: 'low' | 'normal' | 'high'
     */
    public function getPriceIndicator($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);
        $basePrice = $showtime->base_price;
        $currentPrice = $showtime->current_dynamic_price;

        $percentDiff = (($currentPrice - $basePrice) / $basePrice) * 100;

        if ($percentDiff < -5) {
            return 'low';
        } elseif ($percentDiff > 5) {
            return 'high';
        } else {
            return 'normal';
        }
    }

    /**
     * Get price percentage change from base price
     */
    public function getPriceChangePercent($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);
        $basePrice = $showtime->base_price;
        $currentPrice = $showtime->current_dynamic_price;

        return round((($currentPrice - $basePrice) / $basePrice) * 100, 2);
    }

    /**
     * Initialize capacity for a showtime from its screen
     */
    public function initializeCapacity($showtimeId)
    {
        try {
            $showtime = Showtime::with('screen')->findOrFail($showtimeId);

            if (!$showtime->screen || !$showtime->screen->total_seats) {
                Log::warning('Cannot initialize capacity: screen or total_seats missing', [
                    'showtime_id' => $showtimeId,
                ]);
                return false;
            }

            $showtime->update([
                'capacity' => $showtime->screen->total_seats,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to initialize capacity', [
                'showtime_id' => $showtimeId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Batch update all active showtimes (called by scheduler)
     */
    public function updateAllActivePrices()
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        // Get all active showtimes with future start times
        $showtimes = Showtime::where('is_active', 1)
            ->where('start_time', '>', now()->subHours(1))
            ->get();

        foreach ($showtimes as $showtime) {
            if ($this->updateDynamicPrice($showtime->id)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        Log::info('Batch dynamic pricing update completed', $results);

        return $results;
    }
}