<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SeatCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'base_price',
        'description',
    ];
}