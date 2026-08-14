<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reservation_seat_id',
        'qr_token',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function reservationSeat()
    {
        return $this->belongsTo(ReservationSeat::class);
    }
}