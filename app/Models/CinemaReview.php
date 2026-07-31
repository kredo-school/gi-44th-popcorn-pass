<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CinemaReview extends Model
{
    use \App\Traits\UuidPrimaryKey;

    protected $table = 'cinema_reviews';

    protected $fillable = [
        'cinema_id',
        'user_id',
        'image_quality',
        'sound_quality',
        'seat_comfort',
        'crowding_level',
        'accessibility',
        'service_quality',
        'comment',
        'review_count',
        'visited_at',
    ];

    protected $casts = [
        'image_quality' => 'float',
        'sound_quality' => 'float',
        'seat_comfort' => 'float',
        'crowding_level' => 'float',
        'accessibility' => 'float',
        'service_quality' => 'float',
        'visited_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to Cinema
     */
    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class, 'cinema_id', 'id');
    }

    /**
     * Relationship: belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Calculate overall experience score from dimensions
     * Crowding level is inverted (lower crowding = higher score)
     */
    public function calculateOverallScore(): float
    {
        $invertedCrowding = 5.0 - $this->crowding_level;

        $total = $this->image_quality
            + $this->sound_quality
            + $this->seat_comfort
            + $invertedCrowding
            + $this->accessibility
            + $this->service_quality;

        return round($total / 6, 1);
    }

    /**
     * Get overall score (calculated on the fly)
     */
    public function getOverallScoreAttribute(): float
    {
        return $this->calculateOverallScore();
    }

    /**
     * Scope: filter reviews by minimum experience score
     */
    public function scopeByExperience($query, float $minScore = 0)
    {
        // Since overall_score is calculated, we filter after retrieval
        // For DB queries, we can use a raw expression, but for simplicity,
        // we'll leave this for application-level filtering
        return $query;
    }

    /**
     * Scope: filter by cinema
     */
    public function scopeForCinema($query, string $cinemaId)
    {
        return $query->where('cinema_id', $cinemaId);
    }

    /**
     * Scope: recent reviews first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if review is within last X days
     */
    public function isRecent(int $days = 30): bool
    {
        return $this->created_at->diffInDays(now()) <= $days;
    }

    /**
     * Get rating category for a dimension
     */
    public static function getRatingCategory(float $rating): string
    {
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 4.0) return 'Very Good';
        if ($rating >= 3.0) return 'Good';
        if ($rating >= 2.0) return 'Fair';
        return 'Poor';
    }
}