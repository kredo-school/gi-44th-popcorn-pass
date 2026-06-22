<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AgeRating extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'min_age',
        'description',
    ];

    public function movies()
    {
        return $this->hasMany(Movie::class, 'age_rating_id');
    }
}