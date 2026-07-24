<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Showtime;
use App\Models\ReservationSeat;
use App\Models\ShowtimeSeat;
use App\Models\Payment;
use App\Services\DynamicPricingService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    protected $pricingService;

    public function __construct(DynamicPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    // --------------------
    // Showtime Selection Page
    // --------------------
    public function showtimeSelection(Showtime $showtime)
    {
        session(['showtime_id' => $showtime->id]);

        // Store for subsequent screens
        session([
            'showtime_id' => $showtime->id,
        ]);

        // Get reserved seats
        $reservedSeats = Reservation::with('reservationSeats.showtimeSeat.screenSeat')
            ->where('showtime_id', $showtime->id)
            ->get()
            ->flatMap(fn($reservation) => $reservation->seat_numbers)
            ->toArray();

        $selectedSeats = [];

        return view('reservations.seat-selection', compact(
            'selectedSeats',
            'reservedSeats',
            'showtime'
        ));
    }

    // --------------------
    // Seat Selection
    // --------------------
    public function seatSelection(Showtime $showtime)
    {
        $selectedSeats = session('selectedSeats', []);
        session()->forget(['selectedSeats', 'paymentInfo',]);


        session([
            'showtime_id' => $showtime->id,
        ]);

        // Get reserved seats for this showtime
        $reservedSeats = Reservation::with('reservationSeats.showtimeSeat.screenSeat')
            ->where('showtime_id', $showtime->id)
            ->get()
            ->flatMap(fn($reservation) => $reservation->seat_numbers)
            ->toArray();

        return view('reservations.seat-selection', compact(
            'selectedSeats',
            'reservedSeats',
            'showtime'
        ));
    }

    // --------------------
    // Seat Selection Store
    // --------------------
    public function seatSelectionStore(Request $request)
    {
        $selectedSeats = json_decode($request->selectedSeats ?? '[]', true);

        $selectedSeats = array_map(function ($seat) {
            return [
                'seat' => $seat['seat'],
                'premium' => $seat['premium'],
            ];
        }, $selectedSeats);

        session(['selectedSeats' => $selectedSeats]);
        session()->forget('paymentInfo');

        return redirect()->route('reservations.ticket-type');
    }

    // --------------------
    // Ticket Type Page
    // --------------------
    public function ticketType()
    {
        if (empty(session('selectedSeats'))) {
            return redirect()->route('home');
        }

        $selectedSeats = session('selectedSeats', []);

        if (empty($selectedSeats)) {
            return redirect()->route('reservations.seat-selection', [
                'showtime' => session('showtime_id')
            ])->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;

            if (!empty($seat['premium'])) {
                $price += 10;
            }

            return $price;
        });

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail(session('showtime_id'));

        return view('reservations.ticket-type', compact(
            'selectedSeats',
            'totalPrice',
            'showtime'
        ));
    }

    // --------------------
    // Save Ticket
    // --------------------
    public function saveTicket(Request $request)
    {
        $selectedSeats = is_string($request->seats)
            ? json_decode($request->seats, true)
            : $request->seats;

        session(['selectedSeats' => $selectedSeats]);
        session()->forget('paymentInfo');

        if ($request->clearPayment) {
            session()->forget('paymentInfo');
        }

        return response()->json(['status' => 'ok']);
    }

    // --------------------
    // Payment Method
    // --------------------
    public function paymentMethod()
    {
        if (empty(session('selectedSeats'))) {
            return redirect()->route('home');
        }

        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);

        if (empty($selectedSeats)) {
            return redirect()->route('reservations.showtimeSelection', [
                'showtime' => session('showtime_id')
            ])
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;

            if (!empty($seat['premium'])) {
                $price += 10;
            }

            return $price;
        });

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail(session('showtime_id'));

        return view('reservations.payment-method', compact(
            'selectedSeats',
            'totalPrice',
            'paymentInfo',
            'showtime'
        ));
    }

    // --------------------
    // Save Payment
    // --------------------
    public function savePayment(Request $request)
    {
        session(['paymentInfo' => [
            'method' => $request->method,
            'last4'  => $request->last4 ?? null,
            'email'  => $request->email ?? null,
        ]]);

        return response()->json(['status' => 'ok']);
    }

    // --------------------
    // Confirmation Page
    // --------------------
    public function confirmation()
    {
        if (empty(session('selectedSeats'))) {
            return redirect()->route('home');
        }

        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);

        if (empty($selectedSeats) || empty($paymentInfo)) {
            return redirect()->route('reservations.showtimeSelection', [
                'showtime' => session('showtime_id')
            ])
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;
            if (!empty($seat['premium'])) {
                $price += 10;
            }
            return $price;
        });

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail(session('showtime_id'));

        return view('reservations.reservation-confirm', compact(
            'selectedSeats',
            'paymentInfo',
            'totalPrice',
            'showtime'
        ));
    }

    // --------------------
    // Confirm Booking (★ UPDATED WITH DYNAMIC PRICING)
    // --------------------
    public function confirmBooking()
    {
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);
        $showtimeId = session('showtime_id');

        if (empty($selectedSeats) || empty($paymentInfo) || !$showtimeId) {
            return redirect()->route('reservations.showtimeSelection', [
                'showtime' => $showtimeId
            ]);
        }

        $showtime = Showtime::with(['movie', 'screen'])->findOrFail($showtimeId);

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            return ($seat['price'] ?? 0) + (!empty($seat['premium']) ? 10 : 0);
        });

        DB::transaction(function () use ($selectedSeats, $showtime, $totalPrice) {

            // Create reservation
            $reservation = Reservation::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'showtime_id' => $showtime->id,
                'screen_id' => $showtime->screen_id,
                'cinema_id' => $showtime->screen->cinema_id,
                'movie_id' => $showtime->movie_id,
                'reservation_status' => 'confirmed',
                'total_seats' => count($selectedSeats),
                'subtotal' => $totalPrice,
                'final_amount' => $totalPrice,
                'reservation_reference' => strtoupper(Str::random(10)),
                'confirmed_at' => now(),
            ]);

            // ★ Create Payment
            Payment::create([
                'reservation_id' => $reservation->id,

                'coupon_id' => null,
                'promotion_id' => null,

                'subtotal' => $totalPrice,
                'discount_amount' => 0,
                'tax' => 0,
                'amount' => $totalPrice,

                'payment_status' => 'paid',
                'payment_method' => $paymentInfo['payment_method'] ?? 'credit_card',

                'transaction_id' => null,
                'stripe_payment_intent_id' => null,

                'paid_at' => now(),
                'refunded_at' => null,
                'refund_amount' => 0,
            ]);

            session()->put(
                'reservation_reference',
                $reservation->reservation_reference
            );

            // Create reservation seats
            foreach ($selectedSeats as $seat) {
                $showtimeSeat = ShowtimeSeat::where('showtime_id', $showtime->id)

                    ->whereHas('screenSeat', function ($query) use ($seat) {
                        preg_match('/([A-Z]+)(\d+)/', $seat['seat'], $matches);

                        $query->where('seat_row', $matches[1])
                            ->where('seat_position', $matches[2]);
                    })
                    ->first();

                if (!$showtimeSeat) {
                    throw new \Exception("Seat not found: " . $seat['seat']);
                }

                ReservationSeat::create([
                    'id' => Str::uuid(),
                    'reservation_id' => $reservation->id,
                    'showtime_seat_id' => $showtimeSeat->id,
                    'price_at_reservation' => ($seat['price'] ?? 0) + (!empty($seat['premium']) ? 10 : 0),
                ]);

                $showtimeSeat->update([
                    'seat_status' => 'reserved'
                ]);
            }

            // ★ DYNAMIC PRICING: Increment booked seats
            $showtime->increment('booked_seats', count($selectedSeats));

            // ★ DYNAMIC PRICING: Initialize capacity if not set (first booking)
            if ($showtime->capacity == 0 && $showtime->screen->total_seats) {
                $this->pricingService->initializeCapacity($showtime->id);
                $showtime->refresh();
            }

            // ★ DYNAMIC PRICING: Update dynamic price based on new occupancy
            $this->pricingService->updateDynamicPrice($showtime->id);
        });

        // Session cleanup after successful booking
        session()->put('booking_done', true);
        session()->put('final_price', $totalPrice);

        return redirect()->route('reservations.complete', [
            'showtime' => $showtime->id
        ]);
    }

    // --------------------
    // Complete Booking
    // --------------------
    public function complete($showtime)
    {
        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail($showtime);

        $selectedSeats = session('selectedSeats', []);
        $totalPrice = session('final_price', 0);
        $reservationReference = session('reservation_reference');
        session()->forget([
            'selectedSeats',
            'paymentInfo',
            'showtime_id',
        ]);

        return view('reservations.reservation-complete', compact(
            'showtime',
            'selectedSeats',
            'totalPrice',
            'reservationReference'
        ));
    }

    // Cancel tickets from mypage
    public function cancel(Reservation $reservation)
    {
        // 自分の予約以外はキャンセル不可
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // 既にキャンセル済み
        if ($reservation->reservation_status === 'cancelled') {
            return back()->with('error', 'This reservation has already been cancelled.');
        }

        $reservation->update([
            'reservation_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()
    ->route('mypage.tickets')
    ->with('success', 'Reservation cancelled successfully.');
    }
}
