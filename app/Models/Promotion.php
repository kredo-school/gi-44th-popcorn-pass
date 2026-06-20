<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Promotion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'type',
        'discount_value',
        'applicable_genre_id',
        'applicable_movie_id',
        'applicable_seat_category_id',
        'applicable_cinema_id',
        'applicable_screen_type',
        'max_uses',
        'current_uses',
        'min_ticket_purchase',
        'start_date',
        'end_date',
        'promotion_status',
        'created_by_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}