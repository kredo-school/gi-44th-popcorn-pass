<?php

namespace App\Http\Controllers;

use App\Services\MovieRecommendationService;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(MovieRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function getRecommendations()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $limit = request('limit', 5);
        $recommendations = $this->recommendationService->getRecommendationsForUser($user->id, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $recommendations->map(function ($rec) {
                return [
                    'movie_id' => $rec->movie_id,
                    'title' => $rec->movie->title ?? null,
                    'poster_url' => $rec->movie->poster_url ?? null,
                    'recommendation_score' => $rec->recommendation_score,
                ];
            }),
        ]);
    }
}