<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostReply extends Model
{
    protected $fillable = ['post_id', 'user_id', 'body', 'spoiler_flag', 'parent_reply_id'];
    protected $table = 'post_replies';

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parentReply(): BelongsTo
    {
        return $this->belongsTo(PostReply::class, 'parent_reply_id');
    }

    public function childReplies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'parent_reply_id')->orderBy('created_at', 'asc');
    }
}