<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Showtime;
use App\Models\ReservationSeat;
use App\Models\ShowtimeSeat;
use App\Models\Payment;
use App\Services\DynamicPricingService;
use App\Models\Coupon;
use App\Models\Promotion;
use App\Models\UserCoupon;

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
    // Reservation Login
    // --------------------
    public function loginRedirect(Request $request)
    {
        session([
            'showtime_id' => $request->showtime_id,
            'url.intended' => route('reservations.seat-selection', [
                'showtime' => $request->showtime_id,
                'new' => 1,
            ]),
        ]);

        return redirect()->route('login');
    }


    // --------------------
    // Continue as Guest
    // --------------------
    public function guest(Request $request)
    {
        session([
            'showtime_id' => $request->showtime_id,
            'guest' => true,
        ]);

        return redirect()->route('reservations.seat-selection', [
            'showtime' => $request->showtime_id,
            'new' => 1,
        ]);
    }

    // --------------------
    // Seat Selection
    // --------------------
    public function seatSelection(Request $request, Showtime $showtime)
    {
        if ($request->boolean('new')) {
            session()->forget([
                'selectedSeats',
                'paymentInfo',
                'discountInfo',
            ]);
        }

        $selectedSeats = session('selectedSeats', []);

        session(['showtime_id' => $showtime->id]);

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
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);
        $showtimeId = session('showtime_id');

        if (empty($selectedSeats) || !$showtimeId) {
            return redirect()
                ->route('home')
                ->with('error', 'Your reservation session has expired. Please start again.');
        }

        // Subtotal
        $subtotal = collect($selectedSeats)->sum(function ($seat) {
            $price = (float) ($seat['price'] ?? 0);

            if (!empty($seat['premium'])) {
                $price += 10;
            }

            return $price;
        });

        $ticketCount = count($selectedSeats);

        // Showtime
        $showtime = Showtime::with([
            'movie.genres',
            'screen',
        ])->findOrFail($showtimeId);

        $movieGenreIds = $showtime->movie->genres
            ->pluck('id')
            ->toArray();

        // Applicable Promotion
        $promotion = Promotion::query()
            ->where('promotion_status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())

            // Maximum uses
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereColumn('current_uses', '<', 'max_uses');
            })

            // Minimum number of tickets
            ->where(function ($query) use ($ticketCount) {
                $query->whereNull('min_ticket_purchase')
                    ->orWhere('min_ticket_purchase', '<=', $ticketCount);
            })

            // Applicable movie
            ->where(function ($query) use ($showtime) {
                $query->whereNull('applicable_movie_id')
                    ->orWhere('applicable_movie_id', $showtime->movie_id);
            })

            // Applicable genre
            ->where(function ($query) use ($movieGenreIds) {
                $query->whereNull('applicable_genre_id');

                if (!empty($movieGenreIds)) {
                    $query->orWhereIn('applicable_genre_id', $movieGenreIds);
                }
            })

            // Applicable cinema
            ->where(function ($query) use ($showtime) {
                $query->whereNull('applicable_cinema_id')
                    ->orWhere(
                        'applicable_cinema_id',
                        $showtime->screen->cinema_id
                    );
            })

            // More specific promotions first
            ->orderByRaw('
            (
                (applicable_movie_id IS NOT NULL)
                + (applicable_genre_id IS NOT NULL)
                + (applicable_cinema_id IS NOT NULL)
            ) DESC
        ')

            // Higher discount value first
            ->orderByDesc('discount_value')
            ->first();

        // Promotion Discount
        $promotionDiscount = $this->calculatePromotionDiscount(
            $promotion,
            $subtotal
        );

        $totalPrice = max(
            0,
            $subtotal - $promotionDiscount
        );

        // Available Coupons
        $availableCoupons = collect();

        if (Auth::check()) {
            $availableCoupons = Auth::user()
                ->coupons()
                ->wherePivotNull('used_at')
                ->where('coupon_status', 'active')

                // Expiration date
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })

                // Maximum uses
                ->where(function ($query) {
                    $query->whereNull('max_uses')
                        ->orWhereColumn('current_uses', '<', 'max_uses');
                })

                // Nearest expiration first
                ->orderByRaw('expires_at IS NULL, expires_at ASC')
                ->get();
        }

        // Discount Session
        session([
            'discountInfo' => [
                'subtotal' => $subtotal,
                'promotion_id' => $promotion?->id,
                'promotion_discount' => $promotionDiscount,
                'coupon_id' => null,
                'coupon_discount' => 0,
                'discount_amount' => $promotionDiscount,
                'final_amount' => $totalPrice,
            ],
        ]);

        return view('reservations.payment-method', compact(
            'selectedSeats',
            'subtotal',
            'totalPrice',
            'paymentInfo',
            'showtime',
            'promotion',
            'promotionDiscount',
            'availableCoupons'
        ));
    }

    // --------------------
    // Calculate Promotion Discount
    // --------------------
    private function calculatePromotionDiscount(
        ?Promotion $promotion,
        float $subtotal
    ): float {
        if (!$promotion) {
            return 0;
        }

        if ($promotion->type === 'percentage') {
            $discount = $subtotal
                * ((float) $promotion->discount_value / 100);

            return round(
                min($discount, $subtotal),
                2
            );
        }

        if ($promotion->type === 'fixed_amount') {
            return round(
                min(
                    (float) $promotion->discount_value,
                    $subtotal
                ),
                2
            );
        }

        return 0;
    }

    // --------------------
    // Confirmation Page
    // --------------------
    public function confirmation(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,onsite',
            'coupon_id' => 'nullable|uuid',
            'paypal_email' => 'nullable|email',
        ]);

        session([
            'paymentInfo' => [
                'payment_method' => $request->payment_method,
                'email' => $request->payment_method === 'paypal'
                    ? $request->paypal_email
                    : null,
            ],
        ]);

        if (session('guest')) {
            session([
                'guestInfo' => [
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->guest_email,
                    'phone'      => $request->guest_phone,
                ],
            ]);
        }

        $guestInfo = session('guestInfo', []);

        $selectedSeats = session('selectedSeats', []);

        if (empty($selectedSeats)) {
            return redirect()->route('home');
        }

        $paymentInfo = session('paymentInfo', []);
        $discountInfo = session('discountInfo', []);

        if (empty($paymentInfo)) {
            $showtimeId = session('showtime_id');

            if (!$showtimeId) {
                return redirect()
                    ->route('home')
                    ->with('error', 'Your reservation session has expired. Please start again.');
            }

            return redirect()->route('reservations.showtimeSelection', [
                'showtime' => $showtimeId,
            ])->with('error', 'Your session has expired. Please start again.');
        }

        $subtotal = (float) ($discountInfo['subtotal'] ?? 0);
        $promotionDiscount =
            (float) ($discountInfo['promotion_discount'] ?? 0);

        $amountAfterPromotion = max(
            0,
            $subtotal - $promotionDiscount
        );

        $coupon = null;
        $couponDiscount = 0;

        if (Auth::check() && $request->filled('coupon_id')) {
            $coupon = Auth::user()
                ->coupons()
                ->where('coupons.id', $request->coupon_id)
                ->wherePivotNull('used_at')
                ->where('coupon_status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('max_uses')
                        ->orWhereColumn('current_uses', '<', 'max_uses');
                })
                ->first();

            if (!$coupon) {
                return back()->with(
                    'error',
                    'This coupon is not available.'
                );
            }

            if ($coupon->coupon_type === 'percentage') {
                $couponDiscount = $amountAfterPromotion
                    * ((float) $coupon->discount_percent / 100);
            } elseif ($coupon->coupon_type === 'fixed_amount') {
                $couponDiscount =
                    (float) $coupon->discount_amount;
            }

            $couponDiscount = round(
                min($couponDiscount, $amountAfterPromotion),
                2
            );
        }

        $totalPrice = max(
            0,
            $amountAfterPromotion - $couponDiscount
        );

        session([
            'discountInfo' => [
                'subtotal' => $subtotal,

                'promotion_id' =>
                $discountInfo['promotion_id'] ?? null,
                'promotion_discount' => $promotionDiscount,

                'coupon_id' => $coupon?->id,
                'coupon_discount' => $couponDiscount,

                'discount_amount' =>
                $promotionDiscount + $couponDiscount,

                'final_amount' => $totalPrice,
            ],
        ]);

        $showtimeId = session('showtime_id');

        if (!$showtimeId) {
            return redirect()
                ->route('home')
                ->with('error', 'Your reservation session has expired. Please start again.');
        }

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail($showtimeId);

        $promotion = null;

        if (!empty($discountInfo['promotion_id'])) {
            $promotion = Promotion::find(
                $discountInfo['promotion_id']
            );
        }

        return view('reservations.reservation-confirm', compact(
            'selectedSeats',
            'paymentInfo',
            'guestInfo',
            'showtime',
            'subtotal',
            'promotion',
            'promotionDiscount',
            'coupon',
            'couponDiscount',
            'totalPrice'
        ));
    }

    // --------------------
    // Confirm Booking (★ UPDATED WITH DYNAMIC PRICING)
    // --------------------
    public function confirmBooking()
    {
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);
        $guestInfo = session('guestInfo', []);
        $discountInfo = session('discountInfo', []);
        $showtimeId = session('showtime_id');

        if (empty($selectedSeats) || empty($paymentInfo) || !$showtimeId) {
            return redirect()
                ->route('home')
                ->with('error', 'Your reservation session has expired. Please start again.');
        }

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail($showtimeId);


        // Discount information
        $subtotal = (float) ($discountInfo['subtotal'] ?? 0);
        $promotionId = $discountInfo['promotion_id'] ?? null;
        $couponId = $discountInfo['coupon_id'] ?? null;
        $promotionDiscount = (float) ($discountInfo['promotion_discount'] ?? 0);
        $couponDiscount = (float) ($discountInfo['coupon_discount'] ?? 0);
        $discountAmount = (float) ($discountInfo['discount_amount'] ?? 0);
        $finalAmount = (float) ($discountInfo['final_amount'] ?? $subtotal);

        DB::transaction(function () use (
            $selectedSeats,
            $paymentInfo,
            $guestInfo,
            $showtime,
            $subtotal,
            $promotionId,
            $couponId,
            $promotionDiscount,
            $couponDiscount,
            $discountAmount,
            $finalAmount
        ) {
            // Create reservation
            $reservation = Reservation::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),

                'guest_first_name' => $guestInfo['first_name'] ?? null,
                'guest_last_name' => $guestInfo['last_name'] ?? null,
                'guest_email' => $guestInfo['email'] ?? null,
                'guest_phone' => $guestInfo['phone'] ?? null,

                'promotion_id' => $promotionId,
                'coupon_id' => $couponId,

                'showtime_id' => $showtime->id,
                'screen_id' => $showtime->screen_id,
                'cinema_id' => $showtime->screen->cinema_id,
                'movie_id' => $showtime->movie_id,

                'reservation_status' => 'confirmed',
                'total_seats' => count($selectedSeats),

                'subtotal' => $subtotal,
                'promotion_discount' => $promotionDiscount,
                'coupon_discount' => $couponDiscount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,

                'reservation_reference' => strtoupper(Str::random(10)),
                'confirmed_at' => now(),
            ]);

            // ★ Create Payment
            Payment::create([
                'reservation_id' => $reservation->id,

                'coupon_id' => $couponId,
                'promotion_id' => $promotionId,

                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax' => 0,
                'amount' => $finalAmount,

                'payment_status' => 'paid',
                'payment_method' => $paymentInfo['payment_method'],
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
                    'price_at_reservation' => ($seat['price'] ?? 0)
                        + (!empty($seat['premium']) ? 10 : 0),
                ]);

                $showtimeSeat->update([
                    'seat_status' => 'reserved',
                ]);
            }

            // ★ Update Promotion usage
            if ($promotionId) {
                Promotion::where('id', $promotionId)
                    ->increment('current_uses');
            }

            // ★ Update Coupon usage
            if ($couponId && Auth::check()) {

                UserCoupon::where('user_id', Auth::id())
                    ->where('coupon_id', $couponId)
                    ->whereNull('used_at')
                    ->update([
                        'used_at' => now(),
                    ]);

                Coupon::where('id', $couponId)
                    ->increment('current_uses');
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
        session()->put('final_price', $finalAmount);

        return redirect()->route('reservations.complete', [
            'showtime' => $showtime->id,
        ]);
    }

    // --------------------
    // Complete Booking
    // --------------------
    public function complete($showtime)
    {
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);
        $discountInfo = session('discountInfo', []);
        $reservationReference = session('reservation_reference');

        if (empty($selectedSeats) || empty($paymentInfo)) {
            return redirect()
                ->route('home')
                ->with('error', 'Your reservation session has expired. Please start again.');
        }

        $showtime = Showtime::with(['movie', 'screen'])
            ->findOrFail($showtime);

        $subtotal = (float) ($discountInfo['subtotal'] ?? 0);
        $promotionDiscount = (float) ($discountInfo['promotion_discount'] ?? 0);
        $couponDiscount = (float) ($discountInfo['coupon_discount'] ?? 0);
        $totalPrice = (float) ($discountInfo['final_amount'] ?? session('final_price', 0));

        session()->forget([
            'selectedSeats',
            'paymentInfo',
            'guestInfo',
            'discountInfo',
            'guest',
            'showtime_id',
        ]);

        return view('reservations.reservation-complete', compact(
            'showtime',
            'selectedSeats',
            'subtotal',
            'promotionDiscount',
            'couponDiscount',
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
