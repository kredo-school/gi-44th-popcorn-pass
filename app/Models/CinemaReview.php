<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CinemaReview extends Model
{
    use HasUuids;

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

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class, 'cinema_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

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

    public function getOverallScoreAttribute(): float
    {
        return $this->calculateOverallScore();
    }

    public function scopeForCinema($query, string $cinemaId)
    {
        return $query->where('cinema_id', $cinemaId);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function isRecent(int $days = 30): bool
    {
        return $this->created_at->diffInDays(now()) <= $days;
    }

    public static function getRatingCategory(float $rating): string
    {
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 4.0) return 'Very Good';
        if ($rating >= 3.0) return 'Good';
        if ($rating >= 2.0) return 'Fair';
        return 'Poor';
    }
}