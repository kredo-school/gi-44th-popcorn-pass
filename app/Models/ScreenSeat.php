<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ScreenSeat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'screen_id',
        'seat_number',
        'seat_row',
        'seat_position',
        'seat_category_id',
        'price',
        'is_wheelchair_accessible',
        'is_blocked',
    ];

    protected $casts = [
        'is_wheelchair_accessible' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function seatCategory()
    {
        return $this->belongsTo(SeatCategory::class);
    }

    public function showtimeSeats()
    {
        return $this->hasMany(ShowtimeSeat::class);
    }
}