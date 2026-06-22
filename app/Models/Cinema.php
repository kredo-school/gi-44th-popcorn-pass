<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cinema extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'cinema_name',
        'city',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'total_screens',
        'opening_time',
        'closing_time',
        'website_url',
        'is_active',
        'created_by_id',
    ];

    public function screens()
    {
        return $this->hasMany(Screen::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}