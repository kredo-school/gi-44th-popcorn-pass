<?php
// app/Http/Controllers/MyPage/ProfileController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.profile.show', [
            'user' => $user,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }
}