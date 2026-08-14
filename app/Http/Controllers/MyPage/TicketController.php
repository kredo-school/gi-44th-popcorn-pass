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
    /**
     * Display user's ticket reservations.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $tab = $request->query('tab', 'upcoming');

        if (!in_array($tab, ['upcoming', 'past', 'cancelled'], true)) {
            $tab = 'upcoming';
        }

        $startTimeSub = Showtime::select('start_time')
            ->whereColumn('showtimes.id', 'reservations.showtime_id');

        $query = $user->reservations()->with([
            'movie',
            'showtime',
            'screen',
            'cinema',
            'reservationSeats.showtimeSeat.screenSeat',
            'reservationSeats.ticket',
            'payment',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cancelled Tickets
        |--------------------------------------------------------------------------
        */
        if ($tab === 'cancelled') {
            $tickets = $query
                ->where('reservation_status', 'cancelled')
                ->orderByDesc('cancelled_at')
                ->paginate(5)
                ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Past Tickets
        |--------------------------------------------------------------------------
        */
        } elseif ($tab === 'past') {
            $tickets = $query
                ->where(function ($q) {
                    $q->where('reservation_status', 'expired')
                        ->orWhere(function ($q) {
                            $q->where('reservation_status', 'confirmed')
                                ->whereHas(
                                    'showtime',
                                    fn($showtimeQuery) =>
                                    $showtimeQuery->where('start_time', '<=', now())
                                );
                        });
                })
                ->orderByDesc($startTimeSub)
                ->paginate(5)
                ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Tickets
        |--------------------------------------------------------------------------
        */
        } else {
            $tickets = $query
                ->where('reservation_status', 'confirmed')
                ->whereHas(
                    'showtime',
                    fn ($q) => $q->where('start_time', '>', now())
                )
                ->orderBy($startTimeSub)
                ->paginate(5)
                ->withQueryString();
        }

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn ($q) => $q->where('start_time', '>', now())
            )
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn ($q) => $q->where('start_time', '<=', now())
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

    /**
     * Display individual QR tickets for a reservation.
     */
    public function showQrCode(string $id): View|RedirectResponse
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Load Reservation
        |--------------------------------------------------------------------------
        |
        | Important:
        | Each ReservationSeat has exactly one individual Ticket.
        |
        | Reservation
        | ├─ ReservationSeat A5
        | │    └─ Ticket
        | ├─ ReservationSeat A6
        | │    └─ Ticket
        | └─ ReservationSeat A7
        |      └─ Ticket
        |
        */
        $reservation = $user->reservations()
            ->with([
                'movie',
                'showtime',
                'screen',
                'cinema',
                'payment',
                'reservationSeats.showtimeSeat.screenSeat',
                'reservationSeats.ticket',
            ])
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Reservation Validation
        |--------------------------------------------------------------------------
        */
        if (
            $reservation->reservation_status !== 'confirmed'
            || !$reservation->showtime
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

        /*
        |--------------------------------------------------------------------------
        | Showtime Validation
        |--------------------------------------------------------------------------
        |
        | QR tickets remain available while the movie is still running.
        | If end_time is unavailable, start_time is used as a fallback.
        |
        */
        $showtimeExpired = $reservation->showtime->end_time
            ? $reservation->showtime->end_time->lte(now())
            : $reservation->showtime->start_time->lte(now());

        if ($showtimeExpired) {
            return redirect()
                ->route('mypage.tickets', ['tab' => 'past'])
                ->with(
                    'error',
                    'This e-ticket has expired.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Validation
        |--------------------------------------------------------------------------
        */
        if (
            !$reservation->payment
            || $reservation->payment->payment_status !== 'paid'
        ) {
            return redirect()
                ->route('mypage.tickets', ['tab' => 'upcoming'])
                ->with(
                    'error',
                    'The e-ticket will be available after payment is completed.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Individual Ticket Validation
        |--------------------------------------------------------------------------
        |
        | Only ReservationSeats that actually have a Ticket are passed
        | to the QR view.
        |
        */
        $individualTickets = $reservation->reservationSeats
            ->filter(function ($reservationSeat) {
                return $reservationSeat->ticket !== null;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Legacy Reservation Protection
        |--------------------------------------------------------------------------
        |
        | Reservations created before the Individual Ticket feature may not
        | have ticket records. We intentionally do NOT fall back to exposing
        | reservation_reference as a QR credential.
        |
        */
        if ($individualTickets->isEmpty()) {
            return redirect()
                ->route('mypage.tickets', ['tab' => 'upcoming'])
                ->with(
                    'error',
                    'Individual e-tickets are not available for this reservation.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar Counts
        |--------------------------------------------------------------------------
        */
        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn ($q) => $q->where('start_time', '>', now())
            )
            ->count();

        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas(
                'showtime',
                fn ($q) => $q->where('start_time', '<=', now())
            )
            ->count();

        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.tickets.qrcode', [
            'user' => $user,
            'reservation' => $reservation,
            'individualTickets' => $individualTickets,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }
}