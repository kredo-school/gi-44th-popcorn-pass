<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    //--------------------
    // Review Index
    //--------------------
    public function index($movieId)
    {
        $movie = Movie::findOrFail($movieId);

        $reviews = Review::where('movie_id', $movieId)
            ->with('user')
            ->latest()
            ->get();
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();

        return view('reviews.index', compact(
            'movie',
            'reviews',
            'averageRating',
            'totalReviews'
        ));
    }


    //--------------------
    // Create Review
    //--------------------

    public function create($movieId)
    {
        $movie = Movie::findOrFail($movieId);
        return view('reviews.create', compact('movie'));
    }


    //--------------------
    // Store Review
    //--------------------
    public function store(Request $request, $movieId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|max:1000',
        ]);

        Review:: create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'rating' => $request->rating,
            'body' => $request->body,
        ]);

        return redirect()->route('reviews.index', $movieId)->with('success', 'Review submitted successfully!');
    }

    //--------------------
    // Show Review
    //--------------------
    public function show($movieId, $reviewId)
    {
        $movie = Movie::findOrFail($movieId);
        $review = Review::with('user')->findOrFail($reviewId);

        return view('reviews.show', compact('movie', 'review'));
    }

    //--------------------
    // Edit Review
    //--------------------
    public function edit($movieId, $reviewId)
    {
        $movie = Movie::findOrFail($movieId);
        $review = Review::findOrFail($reviewId);

        if (Auth::id() !== $review->user_id) {
            return redirect()->route('reviews.index', $movieId);
        }

        return view('reviews.edit', compact('movie', 'review'));
    }

    //--------------------
    // Update Review
    //--------------------
    public function update(Request $request, $movieId, $reviewId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'required|string|max:1000',
        ]);

        $review = Review::findOrFail($reviewId);

        if (Auth::id() !== $review->user_id) {
            return redirect()->route('reviews.index', $movieId);
        }

        $review->update([
            'rating' => $request->rating,
            'body'   => $request->body,
        ]);

        return redirect()->route('reviews.show', [$movieId, $reviewId])
            ->with('success', 'Review updated successfully!');
    }

}
