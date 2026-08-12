<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\UserCoupon;
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
