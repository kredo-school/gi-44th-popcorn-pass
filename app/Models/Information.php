<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Information extends Model
{
    use HasUuids;

    protected $table = 'information';

    protected $fillable = [
        'title',
        'content',
        'category',
        'status',
        'published_at',
        'created_by_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
    
}
