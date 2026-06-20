<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Review extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'movie_id',
        'user_id',
        'rating',
        'body',
        'title',
        'is_verified_purchase',
        'is_moderated',
        'is_approved',
        'moderated_by_id',
        'moderation_reason',
        'helpful_count',
        'unhelpful_count',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}