<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecommendationController;

Route::middleware('auth')->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'getRecommendations']);
});
