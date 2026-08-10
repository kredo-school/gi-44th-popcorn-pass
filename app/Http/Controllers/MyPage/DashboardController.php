<?php
// app/Http/Controllers/MyPage/DashboardController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Conversation;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $upcomingTickets = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', function ($query) {
                $query->where('start_time', '>', now());
            })
            ->with(['movie', 'showtime'])
            ->withCount('reservationSeats')
            ->orderBy(
                \App\Models\Showtime::select('start_time')
                    ->whereColumn('showtimes.id', 'reservations.showtime_id')
            )
            ->take(2)
            ->get();

        $recentlyWatched = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', function ($query) {
                $query->where('start_time', '<=', now());
            })
            ->with(['movie', 'showtime'])
            ->latest('confirmed_at')
            ->take(3)
            ->get();

        $myReviews = $user->reviews()
            ->with('movie')
            ->latest()
            ->take(3)
            ->get();

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        $coupons = $user->coupons()
            ->wherePivotNull('used_at')
            ->where('coupon_status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->take(3)
            ->get();

        $couponsCount = $user->coupons()
            ->whereNull('used_at')
            ->count();

        // chat notification
        $unreadMessages = 0;

        $conversation = Conversation::where(
            'user_id',
            auth()->id()
        )->first();


        if ($conversation) {
            $unreadMessages = Message::where(
                'conversation_id',
                $conversation->id
            )
                ->where(
                    'sender_type',
                    'staff'
                )
                ->where(
                    'is_read',
                    false
                )
                ->count();
        }    

        return view('mypage.dashboard', [
            'user' => $user,
            'upcomingTickets' => $upcomingTickets,
            'recentlyWatched' => $recentlyWatched,
            'myReviews' => $myReviews,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
            'coupons' => $coupons,
            'couponsCount' => $couponsCount,
            'unreadMessages'  => $unreadMessages
        ]);
    }
}