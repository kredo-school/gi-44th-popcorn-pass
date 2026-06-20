<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'showtime_id',
        'screen_id',
        'cinema_id',
        'movie_id',
        'reservation_status',
        'qr_code',
        'total_seats',
        'subtotal',
        'discount_amount',
        'final_amount',
        'reservation_reference',
        'confirmed_at',
        'cancelled_at',
        'expired_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}