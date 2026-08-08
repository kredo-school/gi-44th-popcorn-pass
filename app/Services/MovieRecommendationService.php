<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use App\Models\UserSimilarity;
use App\Models\Reservation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MovieRecommendationService
{
    public function calculateUserSimilarity($userId1, $userId2): float
    {
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);

        if (!$user1 || !$user2) {
            return 0;
        }

        $user1Movies = Reservation::where('user_id', $userId1)
            ->distinct()
            ->pluck('movie_id')
            ->filter()
            ->toArray();

        $user2Movies = Reservation::where('user_id', $userId2)
            ->distinct()
            ->pluck('movie_id')
            ->filter()
            ->toArray();

        if (empty($user1Movies) || empty($user2Movies)) {
            return 0;
        }

        $commonMovies = array_intersect(
            $user1Movies,
            $user2Movies
        );

        if (empty($commonMovies)) {
            return 0;
        }

        $user1Reviews = Review::whereIn('movie_id', $commonMovies)
            ->where('user_id', $userId1)
            ->pluck('rating', 'movie_id')
            ->toArray();

        $user2Reviews = Review::whereIn('movie_id', $commonMovies)
            ->where('user_id', $userId2)
            ->pluck('rating', 'movie_id')
            ->toArray();

        $dotProduct = 0;
        $normUser1 = 0;
        $normUser2 = 0;

        foreach ($commonMovies as $movieId) {
            $rating1 = (float) ($user1Reviews[$movieId] ?? 0);
            $rating2 = (float) ($user2Reviews[$movieId] ?? 0);

            $dotProduct += $rating1 * $rating2;
            $normUser1 += $rating1 ** 2;
            $normUser2 += $rating2 ** 2;
        }

        $denominator = sqrt($normUser1) * sqrt($normUser2);

        if ($denominator == 0) {
            return 0;
        }

        return max(
            0,
            min(1, $dotProduct / $denominator)
        );
    }

    public function calculateAllUserSimilarities(): void
    {
        $users = User::where('role', 1)
            ->pluck('id')
            ->toArray();

        foreach ($users as $i => $userId1) {
            foreach (array_slice($users, $i + 1) as $userId2) {
                $similarity = $this->calculateUserSimilarity(
                    $userId1,
                    $userId2
                );

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

    public function getRecommendationsForUser(
        $userId,
        $limit = 5
    ): Collection {
        try {
            $limit = max(1, (int) $limit);

            $user = User::find($userId);

            if (!$user) {
                return collect();
            }

            $watchedMovieIds = Reservation::where('user_id', $userId)
                ->distinct()
                ->pluck('movie_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $similarUsers = UserSimilarity::getSimilarUsers(
                $userId,
                0.2
            );

            if (!$similarUsers instanceof Collection) {
                $similarUsers = collect($similarUsers);
            }

            if ($similarUsers->isEmpty()) {
                return $this->getTrendingMovies(
                    $limit,
                    $watchedMovieIds
                );
            }

            $similarUserIds = $similarUsers
                ->pluck('similar_user_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (empty($similarUserIds)) {
                return $this->getTrendingMovies(
                    $limit,
                    $watchedMovieIds
                );
            }

            $recommendedMovieIds = Review::whereIn(
                'user_id',
                $similarUserIds
            )
                ->where('rating', '>=', 4.0)
                ->whereNotIn('movie_id', $watchedMovieIds)
                ->pluck('movie_id')
                ->filter()
                ->unique()
                ->values();

            if ($recommendedMovieIds->isEmpty()) {
                return $this->getTrendingMovies(
                    $limit,
                    $watchedMovieIds
                );
            }

            $recommendedMovies = $recommendedMovieIds
                ->map(function ($movieId) use ($similarUserIds) {
                    $movie = Movie::find($movieId);

                    if (!$movie) {
                        return null;
                    }

                    $avgRating = Review::where(
                        'movie_id',
                        $movieId
                    )
                        ->whereIn('user_id', $similarUserIds)
                        ->avg('rating');

                    return (object) [
                        'movie_id' => $movieId,
                        'recommendation_score' => (float) ($avgRating ?? 0),
                        'movie' => $movie,
                    ];
                })
                ->filter()
                ->sortByDesc('recommendation_score')
                ->take($limit)
                ->values();

            if ($recommendedMovies->isEmpty()) {
                return $this->getTrendingMovies(
                    $limit,
                    $watchedMovieIds
                );
            }

            return $recommendedMovies;

        } catch (\Throwable $e) {
            Log::error('MovieRecommendationService error', [
                'user_id' => $userId,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return collect();
        }
    }

    public function getTrendingMovies(
        $limit = 5,
        $excludeMovieIds = []
    ): Collection {
        try {
            $limit = max(1, (int) $limit);

            return Movie::query()
                ->whereNotIn('id', $excludeMovieIds)
                ->get()
                ->map(function (Movie $movie) {
                    $avgRating = Review::where(
                        'movie_id',
                        $movie->id
                    )->avg('rating');

                    return (object) [
                        'movie_id' => $movie->id,
                        'recommendation_score' => (float) ($avgRating ?? 0),
                        'movie' => $movie,
                    ];
                })
                ->sortByDesc('recommendation_score')
                ->take($limit)
                ->values();

        } catch (\Throwable $e) {
            Log::error('getTrendingMovies error', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return collect();
        }
    }
}