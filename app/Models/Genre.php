<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Genre extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'icon_url',
    ];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}