<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'upcoming');

        $startTimeSub = Showtime::select('start_time')
            ->whereColumn('showtimes.id', 'reservations.showtime_id');

        $query = $user->reservations()
            ->with([
                'movie',
                'showtime',
                'screen',
                'reservationSeats.showtimeSeat.screenSeat',
            ]);

        if ($tab === 'cancelled') {
            $tickets = $query
                ->where('reservation_status', 'cancelled')
                ->orderByDesc('cancelled_at')
                ->paginate(5)
                ->withQueryString();
        } elseif ($tab === 'past') {
            $tickets = $query
                ->where('reservation_status', 'confirmed')
                ->whereHas(
                    'showtime',
                    fn($q) => $q->where('start_time', '<=', now())
                )
                ->orderByDesc($startTimeSub)
                ->paginate(5)
                ->withQueryString();
        } else {
            $tickets = $query
                ->where('reservation_status', 'confirmed')
                ->whereHas(
                    'showtime',
                    fn($q) => $q->where('start_time', '>', now())
                )
                ->orderBy($startTimeSub)
                ->paginate(5)
                ->withQueryString();
        }

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn($q) => $q->where('start_time', '>', now())
            )
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn($q) => $q->where('start_time', '<=', now())
            )
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

    public function showQrCode(string $id): View|RedirectResponse
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with([
                'movie',
                'showtime',
                'screen',
                'reservationSeats.showtimeSeat.screenSeat',
            ])
            ->findOrFail($id);

        if (
            $reservation->reservation_status !== 'confirmed'
            || !$reservation->showtime
            || $reservation->showtime->start_time->lte(now())
        ) {
            $tab = $reservation->reservation_status === 'cancelled'
                ? 'cancelled'
                : 'past';

            return redirect()
                ->route('mypage.tickets', ['tab' => $tab])
                ->with(
                    'error',
                    'This e-ticket is no longer available.'
                );
        }

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn($q) => $q->where('start_time', '>', now())
            )
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn($q) => $q->where('start_time', '<=', now())
            )
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
