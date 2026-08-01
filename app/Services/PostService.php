<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostReply;

class PostService
{
    /**
     * Create a new post for a movie
     */
    public function createPost(string $movieId, string $userId, string $title, string $body, bool $spoilerFlag = false): Post
    {
        return Post::create([
            'movie_id' => $movieId,
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'spoiler_flag' => $spoilerFlag,
        ]);
    }

    /**
     * Get all posts for a movie (paginated)
     */
    public function getPostsByMovie(string $movieId, int $perPage = 10)
    {
        return Post::where('movie_id', $movieId)
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a reply to a post
     */
    public function createReply(string $postId, string $userId, string $body, bool $spoilerFlag = false, ?int $parentReplyId = null): PostReply
    {
        return PostReply::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'body' => $body,
            'spoiler_flag' => $spoilerFlag,
            'parent_reply_id' => $parentReplyId,
        ]);
    }

    /**
     * Get all replies for a post (with nested structure)
     */
    public function getRepliesByPost(string $postId)
    {
        return PostReply::where('post_id', $postId)
            ->with(['user', 'childReplies.user'])
            ->whereNull('parent_reply_id')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Delete a post
     */
    public function deletePost(string $postId, string $userId): bool
    {
        $post = Post::find($postId);
        if (!$post || $post->user_id !== $userId) {
            return false;
        }
        return $post->delete();
    }

    /**
     * Delete a reply
     */
    public function deleteReply(int $replyId, string $userId): bool
    {
        $reply = PostReply::find($replyId);
        if (!$reply || $reply->user_id !== $userId) {
            return false;
        }
        return $reply->delete();
    }
}