<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\InformationCategory;

class Information extends Model
{
    use HasUuids;

    protected $table = 'information';

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'status',
        'published_at',
        'image_url',
        'created_by_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(InformationCategory::class);
    }
    
}
