<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ReservationSeat extends Model
{
    use HasFactory, HasUuids;

    /**
     * reservation_seats テーブルには updated_at カラムが存在しないため、
     * Eloquentにそれを管理させないようにする。
     */
    const UPDATED_AT = null;

    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'showtime_seat_id',
        'price_at_reservation',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function showtimeSeat()
    {
        return $this->belongsTo(ShowtimeSeat::class);
    }
}