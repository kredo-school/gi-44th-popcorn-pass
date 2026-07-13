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
}