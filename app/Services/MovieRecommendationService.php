<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieReview;
use App\Models\User;
use App\Models\UserSimilarity;
use App\Models\MovieWatched;
use Illuminate\Support\Collection;

class MovieRecommendationService
{
    /**
     * Calculate similarity between two users based on:
     * 1. Movies they have watched
     * 2. Review ratings (if both reviewed the same movie)
     * 
     * Uses Cosine Similarity formula
     */
    public function calculateUserSimilarity($userId1, $userId2): float
    {
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);

        if (!$user1 || !$user2) {
            return 0;
        }

        // Get movies watched by both users
        $user1Movies = MovieWatched::where('user_id', $userId1)->pluck('movie_id')->toArray();
        $user2Movies = MovieWatched::where('user_id', $userId2)->pluck('movie_id')->toArray();

        if (empty($user1Movies) || empty($user2Movies)) {
            return 0;
        }

        // Common movies
        $commonMovies = array_intersect($user1Movies, $user2Movies);

        if (empty($commonMovies)) {
            return 0;
        }

        // Get reviews for common movies
        $user1Reviews = MovieReview::whereIn('movie_id', $commonMovies)
            ->where('user_id', $userId1)
            ->pluck('rating', 'movie_id')
            ->toArray();

        $user2Reviews = MovieReview::whereIn('movie_id', $commonMovies)
            ->where('user_id', $userId2)
            ->pluck('rating', 'movie_id')
            ->toArray();

        // Calculate Cosine Similarity
        $dotProduct = 0;
        $normUser1 = 0;
        $normUser2 = 0;

        foreach ($commonMovies as $movieId) {
            $rating1 = $user1Reviews[$movieId] ?? 0;
            $rating2 = $user2Reviews[$movieId] ?? 0;

            $dotProduct += $rating1 * $rating2;
            $normUser1 += $rating1 ** 2;
            $normUser2 += $rating2 ** 2;
        }

        $denominator = sqrt($normUser1) * sqrt($normUser2);

        if ($denominator == 0) {
            return 0;
        }

        $similarity = $dotProduct / $denominator;

        // Normalize to 0-1 range (cosine similarity is -1 to 1, we want 0 to 1)
        return max(0, $similarity);
    }

    /**
     * Calculate all user similarities in the system
     * Should be run as a queued Job (nightly)
     */
    public function calculateAllUserSimilarities(): void
    {
        $users = User::where('role', 1)->pluck('id')->toArray(); // Only customers

        foreach ($users as $i => $userId1) {
            foreach (array_slice($users, $i + 1) as $userId2) {
                $similarity = $this->calculateUserSimilarity($userId1, $userId2);

                // Store or update (always store with smaller ID first)
                $minId = min($userId1, $userId2);
                $maxId = max($userId1, $userId2);

                UserSimilarity::updateOrCreate(
                    [
                        'user_id_1' => $minId,
                        'user_id_2' => $maxId,
                    ],
                    [
                        'similarity_score' => $similarity,
                    ]
                );
            }
        }
    }

    /**
     * Get recommended movies for a user
     * Based on what similar users have watched and rated highly
     */
    public function getRecommendationsForUser($userId, $limit = 5): Collection
    {
        $user = User::find($userId);

        if (!$user) {
            return collect([]);
        }

        // Get user's watched movies
        $watchedMovieIds = MovieWatched::where('user_id', $userId)
            ->pluck('movie_id')
            ->toArray();

        // Get similar users
        $similarUsers = UserSimilarity::getSimilarUsers($userId, 0.2); // minSimilarity = 0.2

        if ($similarUsers->isEmpty()) {
            // Fallback: Get trending movies
            return $this->getTrendingMovies($limit, $watchedMovieIds);
        }

        // Get movies rated highly (4.0+) by similar users
        $similarUserIds = $similarUsers->pluck('similar_user_id')->toArray();

        $recommendedMovies = MovieReview::whereIn('user_id', $similarUserIds)
            ->where('rating', '>=', 4.0)
            ->whereNotIn('movie_id', $watchedMovieIds)
            ->select('movie_id')
            ->distinct()
            ->with('movie')
            ->get()
            ->map(function ($review) {
                $movieId = $review->movie_id;
                // Calculate recommendation score (average rating from similar users)
                $avgRating = MovieReview::where('movie_id', $movieId)
                    ->whereIn('user_id', array_slice($review->pluck('user_id')->toArray(), 0, 10))
                    ->avg('rating');

                return (object)[
                    'movie_id' => $movieId,
                    'recommendation_score' => $avgRating,
                    'movie' => $review->movie,
                ];
            })
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();

        return $recommendedMovies;
    }

    /**
     * Fallback: Get trending movies (highest average rating)
     */
    public function getTrendingMovies($limit = 5, $excludeMovieIds = []): Collection
    {
        return Movie::whereNotIn('id', $excludeMovieIds)
            ->with('reviews')
            ->get()
            ->map(function ($movie) {
                return (object)[
                    'movie_id' => $movie->id,
                    'recommendation_score' => $movie->review_average ?? 0,
                    'movie' => $movie,
                ];
            })
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();
    }
}