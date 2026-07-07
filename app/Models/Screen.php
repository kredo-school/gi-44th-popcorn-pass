<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Screen extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'cinema_id',
        'screen_number',
        'screen_name',
        'screen_type',
        'layout_id',
        'total_seats',
        'is_active',
        'created_by_id',
    ];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function layout()
    {
        return $this->belongsTo(TheaterLayout::class, 'layout_id');
    }

    public function screenSeats()
    {
        return $this->hasMany(ScreenSeat::class);
    }
}