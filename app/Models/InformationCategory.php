<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InformationCategory extends Model
{
    use HasUuids;

    protected $table = 'information_categories';

    protected $fillable = [
        'name',
        'color',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function informations()
    {
        return $this->hasMany(\App\Models\Information::class, 'category_id');
    }

    public function getTextColorAttribute(): string
    {
        $hex = ltrim($this->color, '#');

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $brightness > 186 ? '#000000' : '#FFFFFF';
    }
}