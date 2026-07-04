<?php
// app/Http/Controllers/MyPage/TicketController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'upcoming'); // 'upcoming' or 'past'

        $startTimeSub = Showtime::select('start_time')
            ->whereColumn('showtimes.id', 'reservations.showtime_id');

        $query = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->with(['movie', 'showtime', 'screen', 'reservationSeats.showtimeSeat.screenSeat']);

        if ($tab === 'past') {
            $tickets = $query->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
                ->orderByDesc($startTimeSub)
                ->paginate(5)
                ->withQueryString();
        } else {
            $tickets = $query->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
                ->orderBy($startTimeSub)
                ->paginate(5)
                ->withQueryString();
        }

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.tickets.index', [
            'user' => $user,
            'tickets' => $tickets,
            'tab' => $tab,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }

    public function showQrCode(string $id): View
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with(['movie', 'showtime', 'screen', 'reservationSeats.showtimeSeat.screenSeat'])
            ->findOrFail($id);

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.tickets.qrcode', [
            'user' => $user,
            'reservation' => $reservation,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }
}