<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cinema extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'cinema_name',
        'city',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'total_screens',
        'opening_time',
        'closing_time',
        'website_url',
        'is_active',
        'created_by_id',
        'avg_image_quality',
        'avg_sound_quality',
        'avg_seat_comfort',
        'avg_crowding_level',
        'avg_accessibility',
        'avg_service_quality',
        'avg_experience_score',
        'total_reviews',
        'last_score_update',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
        'avg_image_quality' => 'float',
        'avg_sound_quality' => 'float',
        'avg_seat_comfort' => 'float',
        'avg_crowding_level' => 'float',
        'avg_accessibility' => 'float',
        'avg_service_quality' => 'float',
        'avg_experience_score' => 'float',
        'total_reviews' => 'integer',
        'last_score_update' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CinemaReview::class, 'cinema_id', 'id');
    }

    /**
     * Get average experience score with formatting
     */
    public function getFormattedExperienceScore(): string
    {
        return number_format($this->avg_experience_score, 1);
    }

    /**
     * Check if cinema has enough reviews for reliable score
     */
    public function hasReliableScore(int $minReviews = 5): bool
    {
        return $this->total_reviews >= $minReviews;
    }

    /**
     * Get review count display
     */
    public function getReviewCountDisplay(): string
    {
        if ($this->total_reviews === 0) {
            return 'No reviews yet';
        }
        return $this->total_reviews . ' review' . ($this->total_reviews > 1 ? 's' : '');
    }
}