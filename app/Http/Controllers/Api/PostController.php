<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * GET /api/movies/{movieId}/posts
     * Get all posts for a movie
     */
    public function index(string $movieId)
    {
        $posts = $this->postService->getPostsByMovie($movieId, 15);
        return response()->json($posts);
    }

    /**
     * POST /api/movies/{movieId}/posts
     * Create a new post
     */
    public function store(Request $request, string $movieId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'spoiler_flag' => 'boolean',
        ]);

        try {
            $post = $this->postService->createPost(
                $movieId,
                Auth::id(),
                $request->input('title'),
                $request->input('body'),
                $request->boolean('spoiler_flag', false)
            );

            return response()->json([
                'success' => true,
                'post' => $post->load(['user', 'replies.user']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/posts/{postId}/replies
     * Get all replies for a post
     */
    public function getReplies(string $postId)
    {
        $replies = $this->postService->getRepliesByPost($postId);
        return response()->json($replies);
    }

    /**
     * POST /api/posts/{postId}/replies
     * Create a reply to a post
     */
    public function storeReply(Request $request, string $postId)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
            'spoiler_flag' => 'boolean',
            'parent_reply_id' => 'nullable|integer|exists:post_replies,id',
        ]);

        try {
            $reply = $this->postService->createReply(
                $postId,
                Auth::id(),
                $request->input('body'),
                $request->boolean('spoiler_flag', false),
                $request->input('parent_reply_id')
            );

            return response()->json([
                'success' => true,
                'reply' => $reply->load('user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /api/posts/{postId}
     * Delete a post
     */
    public function destroy(string $postId)
    {
        $success = $this->postService->deletePost($postId, Auth::id());
        
        if (!$success) {
            return response()->json(['error' => 'Unauthorized or post not found'], 403);
        }

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /api/replies/{replyId}
     * Delete a reply
     */
    public function destroyReply(int $replyId)
    {
        $success = $this->postService->deleteReply($replyId, Auth::id());
        
        if (!$success) {
            return response()->json(['error' => 'Unauthorized or reply not found'], 403);
        }

        return response()->json(['success' => true]);
    }
}