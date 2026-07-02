<?php
// app/Http/Controllers/MyPage/RewardsController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Services\TierService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RewardsController extends Controller
{
    public function index(TierService $tierService): View
    {
        $user = Auth::user();

        // サイドバー用カウント（Dashboardと同じロジック）
        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.rewards.index', [
            'user' => $user,
            'tiers' => $tierService->allTiers(), // ['bronze', 'silver', 'gold', 'platinum']
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }
}