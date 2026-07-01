<?php
// app/Http/Controllers/MyPage/ReviewController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
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
            'is_approved' => false, // Admin Panel側の承認待ち想定
        ]);

        return back()->with('success', 'Thanks! Your review has been submitted.');
    }
}