<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    //--------------------
    // Review List
    //--------------------
    public function index($movieId)
    {
        $movie = Movie::findOrFail($movieId);

        $reviews = Review::where('movie_id', $movieId)
            ->with('user')
            ->lastest()
            ->get();
        $avarageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        return view('reviews.index', compact(
            'movie',
            'reviews',
            'avarageRationg',
            'totalReviews'
        ));
    }


    //--------------------
    // Write Review
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

        return rediarect()->route('reviews.index', $movieId)->with('success', 'Review submited successfully!');
    }





}
