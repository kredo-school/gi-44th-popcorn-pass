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

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function ageRating()
    {
        return $this->belongsTo(AgeRating::class, 'age_rating_id');
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Moves movies between statuses based on released_date / end_date:
     * coming_soon -> now_showing
     * anything    -> archived
     */
    public static function syncStatuses(): void
    {
        $today = now()->toDateString();

        static::where('status', 'coming_soon')
            ->whereNotNull('released_date')
            ->whereDate('released_date', '<=', $today)
            ->update(['status' => 'now_showing']);

        static::where('status', '!=', 'archived')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'archived']);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    /**
     * Convert YouTube URL to embed URL
     *
     * Supports:
     * - https://youtu.be/VIDEO_ID
     * - https://www.youtube.com/watch?v=VIDEO_ID
     * - https://www.youtube.com/shorts/VIDEO_ID
     * - https://www.youtube.com/embed/VIDEO_ID
     */
    public function getTrailerEmbedUrlAttribute()
    {
        if (!$this->trailer_url) {
            return null;
        }

        $url = $this->trailer_url;


        // YouTube Shorts
        if (str_contains($url, 'youtube.com/shorts/')) {

            $videoId = explode('youtube.com/shorts/', $url)[1];
            $videoId = explode('?', $videoId)[0];

            return 'https://www.youtube.com/embed/' . $videoId;
        }


        // youtu.be
        if (str_contains($url, 'youtu.be/')) {

            $videoId = explode('youtu.be/', $url)[1];
            $videoId = explode('?', $videoId)[0];

            return 'https://www.youtube.com/embed/' . $videoId;
        }


        // youtube.com/watch?v=
        if (str_contains($url, 'youtube.com/watch')) {

            parse_str(parse_url($url, PHP_URL_QUERY), $query);

            if (!empty($query['v'])) {
                return 'https://www.youtube.com/embed/' . $query['v'];
            }
        }


        // Already embed URL
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }


        return null;
    }
}
