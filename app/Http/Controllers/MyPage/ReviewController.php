<?php
// app/Http/Controllers/MyPage/ReviewController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'movie_id' => ['required', 'exists:movies,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Auth::user()->reviews()->create([
            'movie_id' => $data['movie_id'],
            'rating' => $data['rating'],
            'body' => $data['body'],
            'is_verified_purchase' => true,
            'is_moderated' => false,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Thanks! Your review has been submitted.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $review = Auth::user()->reviews()->findOrFail($id);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $review->update([
            'rating' => $data['rating'],
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Your review has been updated.');
    }
}