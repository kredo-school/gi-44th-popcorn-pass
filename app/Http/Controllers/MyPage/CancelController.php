<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\UserCoupon;
use App\Models\ReservationSeat;
use App\Services\DynamicPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class CancelController extends Controller
{
    public function __construct(
        private DynamicPricingService $pricingService
    ) {}

    private function sidebarCounts($user): array
    {
        return [
            'upcomingTicketsCount' => $user->reservations()
                ->where('reservation_status', 'confirmed')
                ->whereHas(
                    'showtime',
                    fn($q) => $q->where('start_time', '>', now())
                )
                ->count(),

            'moviesWatchedCount' => $user->reservations()
                ->where('reservation_status', 'confirmed')
                ->whereHas(
                    'showtime',
                    fn($q) => $q->where('start_time', '<=', now())
                )
                ->count(),

            'reviewsWrittenCount' => $user->reviews()->count(),
        ];
    }

    public function show(string $id): View|RedirectResponse
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with([
                'movie',
                'showtime',
                'screen',
                'payment',
                'reservationSeats.showtimeSeat.screenSeat',
                'reservationSeats.ticket',
            ])
            ->findOrFail($id);

        if (
            $reservation->reservation_status !== 'confirmed' ||
            !$reservation->showtime
        ) {
            return redirect()
                ->route('mypage.tickets')
                ->with(
                    'error',
                    'This reservation cannot be cancelled.'
                );
        }

        if (
            !$reservation->payment ||
            $reservation->payment->payment_method !== 'onsite' ||
            $reservation->payment->payment_status !== 'pending'
        ) {
            return redirect()
                ->route('mypage.tickets')
                ->with(
                    'error',
                    'Only pending on-site payment reservations can be cancelled.'
                );
        }

        /*
         * Cancellation is allowed until 11:59 PM on the day
         * before the screening.
         * Cancellations are not allowed from midnight
         * on the screening date.
         */
        $cancelDeadline = $reservation->showtime
            ->start_time
            ->copy()
            ->startOfDay();

        if (now()->gte($cancelDeadline)) {
            return redirect()
                ->route('mypage.tickets')
                ->with(
                    'error',
                    'The cancellation period for this reservation has ended.'
                );
        }

        return view('mypage.cancel.show', array_merge(
            [
                'user' => $user,
                'reservation' => $reservation,
            ],
            $this->sidebarCounts($user)
        ));
    }

    public function cancelSeat(string $reservation, string $reservationSeat): RedirectResponse {
        $user = Auth::user();
        $allSeatsCancelled = false;

        try {
            DB::transaction(function () use (
                $user,
                $reservation,
                $reservationSeat,
                &$allSeatsCancelled
            ) {
                $reservationModel = Reservation::query()
                    ->where('id', $reservation)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $reservationModel->reservation_status !==
                    'confirmed'
                ) {
                    throw new \RuntimeException(
                        'This reservation cannot be cancelled.'
                    );
                }

                $showtime = $reservationModel->showtime()
                    ->lockForUpdate()
                    ->first();

                if (!$showtime) {
                    throw new \RuntimeException(
                        'The showtime could not be found.'
                    );
                }

                $payment = $reservationModel->payment()
                    ->lockForUpdate()
                    ->first();

                if (
                    !$payment ||
                    $payment->payment_method !== 'onsite' ||
                    $payment->payment_status !== 'pending'
                ) {
                    throw new \RuntimeException(
                        'Only pending on-site payment tickets can be cancelled.'
                    );
                }

                if (
                    $reservationModel->coupon_id ||
                    $reservationModel->promotion_id
                ) {
                    throw new \RuntimeException(
                        'Individual tickets cannot be cancelled when a coupon or promotion has been applied. Please cancel the entire booking instead.'
                    );
                }

                $cancelDeadline = $showtime->start_time
                    ->copy()
                    ->startOfDay();

                if (now()->gte($cancelDeadline)) {
                    throw new \RuntimeException(
                        'The cancellation period has ended.'
                    );
                }

                $seat = ReservationSeat::query()
                    ->where('id', $reservationSeat)
                    ->where(
                        'reservation_id',
                        $reservationModel->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($seat->cancelled_at !== null) {
                    throw new \RuntimeException(
                        'This ticket has already been cancelled.'
                    );
                }

                $showtimeSeat = $seat->showtimeSeat()
                    ->lockForUpdate()
                    ->first();

                if (!$showtimeSeat) {
                    throw new \RuntimeException(
                        'The reserved seat could not be found.'
                    );
                }

                $seat->update([
                    'cancelled_at' => now(),
                ]);

                $showtimeSeat->update([
                    'seat_status' => 'available',
                ]);

                $showtime->update([
                    'booked_seats' => max(
                        0,
                        (int) $showtime->booked_seats - 1
                    ),
                ]);

                $remainingSeats = $reservationModel
                    ->reservationSeats()
                    ->whereNull('cancelled_at')
                    ->get();

                $remainingCount = $remainingSeats->count();

                if ($remainingCount === 0) {
                    $allSeatsCancelled = true;

                    if ($reservationModel->coupon_id) {
                        UserCoupon::where(
                            'user_id',
                            $user->id
                        )
                            ->where(
                                'coupon_id',
                                $reservationModel->coupon_id
                            )
                            ->whereNotNull('used_at')
                            ->update([
                                'used_at' => null,
                            ]);

                        Coupon::where(
                            'id',
                            $reservationModel->coupon_id
                        )
                            ->where('current_uses', '>', 0)
                            ->decrement('current_uses');
                    }

                    if ($reservationModel->promotion_id) {
                        Promotion::where(
                            'id',
                            $reservationModel->promotion_id
                        )
                            ->where('current_uses', '>', 0)
                            ->decrement('current_uses');
                    }

                    $payment->update([
                        'subtotal' => 0,
                        'discount_amount' => 0,
                        'amount' => 0,
                        'payment_status' => 'cancelled',
                    ]);

                    $reservationModel->update([
                        'total_seats' => 0,
                        'subtotal' => 0,
                        'promotion_discount' => 0,
                        'coupon_discount' => 0,
                        'discount_amount' => 0,
                        'final_amount' => 0,
                        'reservation_status' => 'cancelled',
                        'cancelled_at' => now(),
                    ]);
                } else {
                    $subtotal = round(
                        (float) $remainingSeats->sum(
                            'price_at_reservation'
                        ),
                        2
                    );

                    $promotion = $reservationModel->promotion;
                    $promotionDiscount = 0;

                    $promotionStillApplies =
                        $promotion &&
                        (
                            $promotion->min_ticket_purchase === null ||
                            (int) $promotion->min_ticket_purchase
                            <= $remainingCount
                        );

                    if ($promotionStillApplies) {
                        if ($promotion->type === 'percentage') {
                            $promotionDiscount = $subtotal
                                * (
                                    (float) $promotion->discount_value
                                    / 100
                                );
                        } elseif (
                            $promotion->type === 'fixed_amount'
                        ) {
                            $promotionDiscount =
                                (float) $promotion->discount_value;
                        }

                        $promotionDiscount = round(
                            min($promotionDiscount, $subtotal),
                            2
                        );
                    } elseif ($promotion) {
                        Promotion::where('id', $promotion->id)
                            ->where('current_uses', '>', 0)
                            ->decrement('current_uses');

                        $reservationModel->promotion_id = null;
                        $payment->promotion_id = null;
                    }

                    $amountAfterPromotion = max(
                        0,
                        $subtotal - $promotionDiscount
                    );

                    $coupon = $reservationModel->coupon;
                    $couponDiscount = 0;

                    if ($coupon) {
                        if (
                            $coupon->coupon_type === 'percentage'
                        ) {
                            $couponDiscount =
                                $amountAfterPromotion
                                * (
                                    (float) $coupon->discount_percent
                                    / 100
                                );
                        } elseif (
                            $coupon->coupon_type === 'fixed_amount'
                        ) {
                            $couponDiscount =
                                (float) $coupon->discount_amount;
                        }

                        $couponDiscount = round(
                            min(
                                $couponDiscount,
                                $amountAfterPromotion
                            ),
                            2
                        );
                    }

                    $discountAmount = round(
                        $promotionDiscount + $couponDiscount,
                        2
                    );

                    $finalAmount = round(
                        max(
                            0,
                            $amountAfterPromotion - $couponDiscount
                        ),
                        2
                    );

                    $reservationModel->fill([
                        'total_seats' => $remainingCount,
                        'subtotal' => $subtotal,
                        'promotion_discount' =>
                        $promotionDiscount,
                        'coupon_discount' => $couponDiscount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                    ])->save();

                    $payment->fill([
                        'subtotal' => $subtotal,
                        'discount_amount' => $discountAmount,
                        'amount' => $finalAmount,
                    ])->save();
                }

                $this->pricingService->updateDynamicPrice(
                    $showtime->id
                );
            });
        } catch (Throwable $e) {
            if (!$e instanceof \RuntimeException) {
                report($e);
            }

            return redirect()
                ->route('mypage.cancel.show', $reservation)
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'The ticket could not be cancelled.'
                );
        }

        if ($allSeatsCancelled) {
            return redirect()
                ->route(
                    'mypage.cancel.complete',
                    $reservation
                );
        }

        return redirect()
            ->route('mypage.cancel.show', $reservation)
            ->with(
                'success',
                'The selected ticket was cancelled successfully.'
            );
    }

    public function cancel(string $id): RedirectResponse
    {
        $user = Auth::user();
        $showtimeId = null;

        try {
            DB::transaction(function () use (
                $user,
                $id,
                &$showtimeId
            ) {
                /*
                 * Lock the reservation so that the cancellation
                 * cannot be processed twice.
                 */
                $reservation = Reservation::query()
                    ->where('id', $id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($reservation->reservation_status === 'cancelled') {
                    throw new \RuntimeException(
                        'This reservation has already been cancelled.'
                    );
                }

                if ($reservation->reservation_status !== 'confirmed') {
                    throw new \RuntimeException(
                        'This reservation cannot be cancelled.'
                    );
                }

                $showtime = $reservation->showtime()
                    ->lockForUpdate()
                    ->first();

                if (!$showtime) {
                    throw new \RuntimeException(
                        'The showtime for this reservation could not be found.'
                    );
                }

                $payment = $reservation->payment()
                    ->lockForUpdate()
                    ->first();

                /*
                 * Only pending on-site payments can be cancelled.
                 * PayPal and other paid reservations are rejected.
                 */
                if (
                    !$payment ||
                    $payment->payment_method !== 'onsite' ||
                    $payment->payment_status !== 'pending'
                ) {
                    throw new \RuntimeException(
                        'Only pending on-site payment reservations can be cancelled.'
                    );
                }

                /*
                 * Cancellation is allowed until 11:59 PM on the day
                 * before the screening.
                 */
                $cancelDeadline = $showtime->start_time
                    ->copy()
                    ->startOfDay();

                if (now()->gte($cancelDeadline)) {
                    throw new \RuntimeException(
                        'The cancellation period for this reservation has ended.'
                    );
                }

                $reservationSeats = $reservation->reservationSeats()
                    ->with('showtimeSeat')
                    ->get();

                /*
                 * Release the seats so they can be sold again.
                 */
                foreach ($reservationSeats as $reservationSeat) {
                    $reservationSeat->update([
                        'cancelled_at' => now(),
                    ]);

                    $reservationSeat->showtimeSeat?->update([
                        'seat_status' => 'available',
                    ]);
                }

                /*
                 * Decrease the number of booked seats without allowing
                 * it to become negative.
                 */
                $releasedSeatCount = $reservationSeats->count();

                $showtime->update([
                    'booked_seats' => max(
                        0,
                        (int) $showtime->booked_seats - $releasedSeatCount
                    ),
                ]);

                /*
                 * Return the coupon to the user.
                 */
                if ($reservation->coupon_id) {
                    UserCoupon::where('user_id', $user->id)
                        ->where('coupon_id', $reservation->coupon_id)
                        ->whereNotNull('used_at')
                        ->update([
                            'used_at' => null,
                        ]);

                    Coupon::where('id', $reservation->coupon_id)
                        ->where('current_uses', '>', 0)
                        ->decrement('current_uses');
                }

                /*
                 * Return the promotion usage count.
                 */
                if ($reservation->promotion_id) {
                    Promotion::where('id', $reservation->promotion_id)
                        ->where('current_uses', '>', 0)
                        ->decrement('current_uses');
                }

                $payment->update([
                    'payment_status' => 'cancelled',
                ]);

                $reservation->update([
                    'reservation_status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                $showtimeId = $showtime->id;

                /*
                 * Recalculate the dynamic price after releasing seats.
                 */
                $this->pricingService->updateDynamicPrice(
                    $showtime->id
                );
            });
        } catch (Throwable $e) {
            /*
             * Expected validation failures are shown to the user.
             * Unexpected failures are also recorded in the log.
             */
            if (!$e instanceof \RuntimeException) {
                report($e);
            }

            return redirect()
                ->route('mypage.tickets')
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'The reservation could not be cancelled. Please try again.'
                );
        }

        return redirect()
            ->route('mypage.cancel.complete', $id);
    }

    public function complete(string $id): View
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with([
                'movie',
                'showtime',
                'payment',
            ])
            ->where('reservation_status', 'cancelled')
            ->findOrFail($id);

        return view('mypage.cancel.complete', array_merge(
            [
                'user' => $user,
                'reservation' => $reservation,
            ],
            $this->sidebarCounts($user)
        ));
    }
}
