<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TheaterLayout extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'layout_name',
        'description',
        'total_seats',
        'rows',
        'seats_per_row',
        'is_template',
        'created_by_id',
    ];

    public function screens()
    {
        return $this->hasMany(Screen::class, 'layout_id');
    }
}