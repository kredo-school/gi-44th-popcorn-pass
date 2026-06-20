<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Showtime extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'screen_id',
        'movie_id',
        'start_time',
        'end_time',
        'is_active',
        'created_by_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}