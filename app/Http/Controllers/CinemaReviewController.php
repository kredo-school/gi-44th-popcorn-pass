<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\CinemaReview;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CinemaReviewController extends Controller
{
    /**
     * Store a newly created review in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cinema_id' => 'required|string|exists:cinemas,id',
            'image_quality' => 'required|numeric|between:1.0,5.0',
            'sound_quality' => 'required|numeric|between:1.0,5.0',
            'seat_comfort' => 'required|numeric|between:1.0,5.0',
            'crowding_level' => 'required|numeric|between:1.0,5.0',
            'accessibility' => 'required|numeric|between:1.0,5.0',
            'service_quality' => 'required|numeric|between:1.0,5.0',
            'comment' => 'nullable|string|max:1000',
            'visited_at' => 'required|date|before_or_equal:today',
            'review_count' => 'nullable|integer|min:1|max:100',
        ]);

        // Verify user has visited this cinema (check reservation history)
        $hasVisited = Reservation::where('user_id', auth()->id())
            ->whereHas('movie.screenings', function ($query) use ($validated) {
                $query->where('cinema_id', $validated['cinema_id']);
            })
            ->exists();

        if (!$hasVisited) {
            return response()->json([
                'error' => 'You must have purchased a ticket for this cinema to leave a review.',
            ], 403);
        }

        // Check if review already exists for this user+cinema+date
        $existingReview = CinemaReview::where('user_id', auth()->id())
            ->where('cinema_id', $validated['cinema_id'])
            ->where('visited_at', $validated['visited_at'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'error' => 'You have already reviewed this cinema for this date.',
                'review_id' => $existingReview->id,
            ], 409);
        }

        // Create review
        $review = CinemaReview::create([
            'id' => Str::uuid(),
            'cinema_id' => $validated['cinema_id'],
            'user_id' => auth()->id(),
            'image_quality' => (float) $validated['image_quality'],
            'sound_quality' => (float) $validated['sound_quality'],
            'seat_comfort' => (float) $validated['seat_comfort'],
            'crowding_level' => (float) $validated['crowding_level'],
            'accessibility' => (float) $validated['accessibility'],
            'service_quality' => (float) $validated['service_quality'],
            'comment' => $validated['comment'] ?? null,
            'review_count' => $validated['review_count'] ?? 1,
            'visited_at' => $validated['visited_at'],
        ]);

        // Trigger job to update cinema aggregate scores
        \App\Jobs\UpdateCinemaScores::dispatch($validated['cinema_id']);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => [
                'id' => $review->id,
                'overall_score' => $review->calculateOverallScore(),
                'cinema_id' => $review->cinema_id,
            ],
        ], 201);
    }

    /**
     * Get all reviews for a specific cinema with pagination
     */
    public function show($cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        $reviews = CinemaReview::forCinema($cinemaId)
            ->with('user')
            ->latest()
            ->paginate(10);

        // Add calculated overall scores
        $reviews->getCollection()->transform(function ($review) {
            return $review->append('overall_score');
        });

        return response()->json([
            'cinema' => [
                'id' => $cinema->id,
                'cinema_name' => $cinema->cinema_name,
                'avg_experience_score' => $cinema->avg_experience_score,
                'total_reviews' => $cinema->total_reviews,
                'review_count_display' => $cinema->getReviewCountDisplay(),
                'has_reliable_score' => $cinema->hasReliableScore(),
                'dimensions' => [
                    'image_quality' => $cinema->avg_image_quality,
                    'sound_quality' => $cinema->avg_sound_quality,
                    'seat_comfort' => $cinema->avg_seat_comfort,
                    'crowding_level' => $cinema->avg_crowding_level,
                    'accessibility' => $cinema->avg_accessibility,
                    'service_quality' => $cinema->avg_service_quality,
                ],
            ],
            'reviews' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
            ],
        ]);
    }

    /**
     * Get score breakdown for a cinema
     */
    public function getScoreBreakdown($cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        $reviews = CinemaReview::forCinema($cinemaId)->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'cinema_id' => $cinemaId,
                'total_reviews' => 0,
                'message' => 'No reviews yet',
                'breakdown' => null,
            ]);
        }

        return response()->json([
            'cinema_id' => $cinemaId,
            'cinema_name' => $cinema->cinema_name,
            'total_reviews' => $cinema->total_reviews,
            'breakdown' => [
                'overall_score' => [
                    'value' => $cinema->avg_experience_score,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_experience_score),
                ],
                'image_quality' => [
                    'value' => $cinema->avg_image_quality,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_image_quality),
                ],
                'sound_quality' => [
                    'value' => $cinema->avg_sound_quality,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_sound_quality),
                ],
                'seat_comfort' => [
                    'value' => $cinema->avg_seat_comfort,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_seat_comfort),
                ],
                'crowding_level' => [
                    'value' => $cinema->avg_crowding_level,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_crowding_level),
                    'note' => 'Lower is better (less crowded)',
                ],
                'accessibility' => [
                    'value' => $cinema->avg_accessibility,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_accessibility),
                ],
                'service_quality' => [
                    'value' => $cinema->avg_service_quality,
                    'category' => CinemaReview::getRatingCategory($cinema->avg_service_quality),
                ],
            ],
            'last_updated' => $cinema->last_score_update,
        ]);
    }

    /**
     * Get user's own review for a cinema
     */
    public function getUserReview($cinemaId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $review = CinemaReview::where('user_id', auth()->id())
            ->where('cinema_id', $cinemaId)
            ->orderBy('visited_at', 'desc')
            ->first();

        if (!$review) {
            return response()->json([
                'message' => 'No review found',
                'review' => null,
            ]);
        }

        return response()->json([
            'review' => [
                'id' => $review->id,
                'cinema_id' => $review->cinema_id,
                'image_quality' => $review->image_quality,
                'sound_quality' => $review->sound_quality,
                'seat_comfort' => $review->seat_comfort,
                'crowding_level' => $review->crowding_level,
                'accessibility' => $review->accessibility,
                'service_quality' => $review->service_quality,
                'comment' => $review->comment,
                'overall_score' => $review->calculateOverallScore(),
                'visited_at' => $review->visited_at->format('Y-m-d'),
                'created_at' => $review->created_at,
            ],
        ]);
    }

    /**
     * Get all reviews by the authenticated user
     */
    public function getUserReviews()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reviews = CinemaReview::where('user_id', auth()->id())
            ->with('cinema')
            ->latest()
            ->paginate(10);

        return response()->json([
            'reviews' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Get cinemas sorted by experience score
     */
    public function getTopRatedCinemas(Request $request)
    {
        $limit = $request->query('limit', 10);
        $minReviews = $request->query('min_reviews', 5);

        $cinemas = Cinema::where('total_reviews', '>=', $minReviews)
            ->where('is_active', true)
            ->orderBy('avg_experience_score', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($cinema) {
                return [
                    'id' => $cinema->id,
                    'cinema_name' => $cinema->cinema_name,
                    'address' => $cinema->address,
                    'avg_experience_score' => $cinema->avg_experience_score,
                    'total_reviews' => $cinema->total_reviews,
                    'formatted_score' => $cinema->getFormattedExperienceScore(),
                ];
            });

        return response()->json([
            'cinemas' => $cinemas,
            'count' => $cinemas->count(),
        ]);
    }
}