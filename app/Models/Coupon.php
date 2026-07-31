<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'discount_percent',
        'discount_amount',
        'coupon_type',
        'max_uses',
        'current_uses',
        'coupon_status',
        'issued_at',
        'expires_at',
        'issued_by_id',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot('used_at')
            ->withTimestamps();
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }
}