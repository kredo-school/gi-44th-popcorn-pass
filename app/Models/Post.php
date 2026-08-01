<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'movie_id', 'user_id', 'title', 'body', 'spoiler_flag'];  // 'id' を追加

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'post_id')->whereNull('parent_reply_id')->orderBy('created_at', 'desc');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'post_id')->orderBy('created_at', 'desc');
    }
}