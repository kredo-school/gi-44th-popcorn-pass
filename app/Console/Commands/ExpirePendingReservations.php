<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Coupon;
use App\Models\Promotion;
use App\Models\UserCoupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpirePendingReservations extends Command
{
    protected $signature = 'reservations:expire-pending';

    protected $description =
    'Expire pending on-site reservations after their showtime has ended';

    public function handle(): int
    {
        $expiredCount = 0;

        $reservationIds = Reservation::query()
            ->where('reservation_status', 'confirmed')
            ->whereHas('payment', function ($query) {
                $query
                    ->where('payment_method', 'onsite')
                    ->where('payment_status', 'pending');
            })
            ->whereHas('showtime', function ($query) {
                $query->where('end_time', '<=', now());
            })
            ->pluck('id');

        foreach ($reservationIds as $reservationId) {
            try {
                DB::transaction(function () use (
                    $reservationId,
                    &$expiredCount
                ) {
                    $reservation = Reservation::query()
                        ->where('id', $reservationId)
                        ->lockForUpdate()
                        ->first();

                    if (
                        !$reservation ||
                        $reservation->reservation_status !== 'confirmed'
                    ) {
                        return;
                    }

                    $payment = $reservation->payment()
                        ->lockForUpdate()
                        ->first();

                    $showtime = $reservation->showtime;

                    if (
                        !$payment ||
                        $payment->payment_method !== 'onsite' ||
                        $payment->payment_status !== 'pending' ||
                        !$showtime ||
                        $showtime->end_time->isFuture()
                    ) {
                        return;
                    }

                    /*
                    * Return the coupon because the reservation expired
                    * before payment was completed.
                    */
                    if ($reservation->coupon_id && $reservation->user_id) {
                        UserCoupon::where('user_id', $reservation->user_id)
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

                    $reservation->update([
                        'reservation_status' => 'expired',
                        'expired_at' => now(),
                    ]);

                    $payment->update([
                        'payment_status' => 'expired',
                    ]);

                    $expiredCount++;
                });
            } catch (Throwable $e) {
                report($e);

                $this->error(
                    "Reservation {$reservationId} could not be expired."
                );
            }
        }

        $this->info(
            "{$expiredCount} pending reservation(s) expired."
        );

        return self::SUCCESS;
    }
}
