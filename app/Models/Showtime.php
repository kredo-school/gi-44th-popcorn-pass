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
        'base_price',
        'elasticity_factor',
        'current_dynamic_price',
        'occupancy_rate',
        'capacity',
        'booked_seats',
        'last_price_update',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'elasticity_factor' => 'decimal:2',
        'current_dynamic_price' => 'decimal:2',
        'occupancy_rate' => 'decimal:2',
        'last_price_update' => 'datetime',
    ];

    // Relationships
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

    public function reservationSeats()
    {
        return $this->hasMany(ReservationSeat::class, 'showtime_id');
    }

    public function showtimeSeats()
    {
        return $this->hasMany(ShowtimeSeat::class);
    }
}
