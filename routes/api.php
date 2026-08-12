<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Api\PostController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get(
        '/recommendations',
        [RecommendationController::class, 'getRecommendations']
    );
});

// Community Discussion - Posts & Replies (No auth middleware for testing)
Route::get('/movies/{movieId}/posts', [PostController::class, 'index']);
Route::post('/movies/{movieId}/posts', [PostController::class, 'store']);
Route::get('/posts/{postId}/replies', [PostController::class, 'getReplies']);
Route::post('/posts/{postId}/replies', [PostController::class, 'storeReply']);
Route::delete('/posts/{postId}', [PostController::class, 'destroy']);
Route::delete('/replies/{replyId}', [PostController::class, 'destroyReply']);