<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',

        // Guest information
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',

        // Discount information
        'promotion_id',
        'coupon_id',
        'promotion_discount',
        'coupon_discount',

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
        'subtotal' => 'decimal:2',
        'promotion_discount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
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

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reservationSeats()
    {
        return $this->hasMany(ReservationSeat::class);
    }

    public function getSeatNumbersAttribute()
    {
        return $this->reservationSeats
            ->map(function ($reservationSeat) {
                return $reservationSeat->showtimeSeat->screenSeat->seat_number ?? null;
            })
            ->filter()
            ->values();
    }
}
