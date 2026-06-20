<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ShowtimeSeat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'showtime_id',
        'screen_seat_id',
        'seat_status',
        'price_at_showtime',
    ];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function screenSeat()
    {
        return $this->belongsTo(ScreenSeat::class);
    }

    public function reservationSeats()
    {
        return $this->hasMany(ReservationSeat::class);
    }
}