<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Movie extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'genre_id',
        'duration',
        'synopsis',
        'director',
        'age_rating_id',
        'released_date',
        'end_date',
        'poster_url',
        'banner_image_url',
        'trailer_url',
        'cast',
        'status',
        'is_featured',
        'priority_order',
        'budget',
        'box_office',
        'review_average',
        'total_reviews',
        'popularity_score',
        'search_keywords',
        'created_by_id',
    ];

    protected $casts = [
        'cast' => 'array',
        'search_keywords' => 'array',
        'is_featured' => 'boolean',
        'released_date' => 'date',
        'end_date' => 'date',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function ageRating()
    {
        return $this->belongsTo(AgeRating::class, 'age_rating_id');
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
