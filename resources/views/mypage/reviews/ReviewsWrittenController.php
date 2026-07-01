<?php
// app/Http/Controllers/MyPage/ReviewsWrittenController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewsWrittenController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $sort = $request->query('sort', 'recent');

        $reviews = $user->reviews()
            ->with('movie')
            ->when($sort === 'oldest', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->paginate(5)
            ->withQueryString();

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.reviews.index', [
            'user' => $user,
            'reviews' => $reviews,
            'sort' => $sort,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }
}