<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reservation_id',
        'coupon_id',
        'promotion_id',
        'subtotal',
        'discount_amount',
        'tax',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'paypal_order_id',
        'stripe_payment_intent_id',
        'paid_at',
        'refunded_at',
        'refund_amount',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}