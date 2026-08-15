@extends('layouts.mypage')

@section('title', 'Cancel')

@section('content')
    <div class="mb-4">
        <h2><i class="fa-solid fa-calendar-xmark me-2"></i>Cancel</h2>
        <p class="text-muted">Manage your booking for <strong class="text-white">{{ $reservation->movie->title }}</strong></p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="alert alert-info text-start mb-4">
        <h6 class="fw-bold mb-2">
            <i class="fa-solid fa-circle-info me-1"></i>
            Cancellation Policy
        </h6>
    
        <ul class="mb-0 ps-3">
            <li>Cancellation is available only for unpaid Pay On-Site bookings.</li>
            <li>Bookings must be cancelled before the day of the showtime.</li>
            <li>
                Individual tickets cannot be cancelled when a coupon or
                promotion has been applied.
            </li>
            <li>Used or already cancelled tickets cannot be cancelled.</li>
        </ul>
    </div>

    {{-- Booking Summary --}}
    <div class="mypage-card p-4 mb-4">
        <h5 class="mb-3">Booking Summary</h5>

        <div class="d-flex gap-3 align-items-start">
            @if ($reservation->movie->poster_url)
            <img src="{{ $reservation->movie->poster_url }}" alt="{{ $reservation->movie->title }}"
                class="mypage-watched-row-poster">
            @else
            <div class="mypage-watched-row-poster mypage-poster-placeholder">
                <i class="fa-solid fa-film"></i>
            </div>
            @endif

            <div class="flex-grow-1">
                <div class="fw-bold mb-1">
                    {{ $reservation->movie->title }}
                </div>

                <div class="small text-muted">
                    <i class="fa-solid fa-calendar me-1"></i>
                    {{ $reservation->showtime->start_time->format('M d, Y (D)') }}
                </div>

                <div class="small text-muted">
                    <i class="fa-solid fa-clock me-1"></i>
                    {{ $reservation->showtime->start_time->format('h:i A') }}
                </div>

                <div class="small text-muted">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    Screen {{ $reservation->screen->screen_number ?? '-' }}
                </div>

                <div class="small text-muted">
                    <i class="fa-solid fa-receipt me-1"></i>
                    Booking ID:
                    {{ $reservation->reservation_reference ?? $reservation->id }}
                </div>

                <div class="mt-4">
                    <h6 class="mb-3">Reserved Tickets</h6>

                    @php
                        $hasBookingDiscount =
                        $reservation->coupon_id ||
                        $reservation->promotion_id;
                    @endphp

                    @if ($hasBookingDiscount)
                        <div class="alert alert-info">
                            Individual ticket cancellation is unavailable
                            because a coupon or promotion has been applied.
                            You can still cancel the entire booking.
                        </div>

                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">
                                    Booking Total
                                </span>

                                <span class="fw-bold fs-5">
                                    ${{ number_format($reservation->final_amount, 2) }}
                                </span>
                            </div>

                            <div class="small text-muted mt-1">
                                Total after coupon and promotion discounts
                            </div>
                        </div>
                    @endif

                    @foreach ($reservation->reservationSeats as $reservationSeat)
                        @php
                            $screenSeat =
                            $reservationSeat->showtimeSeat?->screenSeat;

                            $seatNumber =
                            $screenSeat?->seat_number
                            ?? (
                            ($screenSeat?->seat_row ?? '')
                            . ($screenSeat?->seat_position ?? '')
                            );

                            $cancelModalId =
                            'cancelSeatModal-' . $loop->index;
                        @endphp

                        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                            <div>
                                <div class="fw-bold">
                                    Seat {{ $seatNumber ?: '-' }}
                                </div>

                                @if (!$hasBookingDiscount)
                                    <div class="small text-muted">
                                        ${{ number_format($reservationSeat->price_at_reservation, 2) }}
                                    </div>
                                @endif
                            </div>

                            @if ($reservationSeat->cancelled_at)
                                <span class="badge bg-secondary">
                                    Cancelled
                                </span>
                            @elseif ($hasBookingDiscount)
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                    Individual Cancellation Unavailable
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#{{ $cancelModalId }}">
                                    <i class="fa-solid fa-xmark me-1"></i>
                                    Cancel Ticket
                                </button>
                            @endif
                        </div>

                        @if (!$reservationSeat->cancelled_at && !$hasBookingDiscount)
                            <div class="modal fade" id="{{ $cancelModalId }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content mypage-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Cancel Ticket
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            Are you sure you want to cancel
                                            Seat {{ $seatNumber }}?
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn mypage-btn-back" data-bs-dismiss="modal">
                                                Keep Ticket
                                            </button>

                                            <form method="POST" action="{{ route('mypage.cancel.seat', ['reservation' => $reservation->id, 'reservationSeat' => $reservationSeat->id, ]) }}">
                                                @csrf

                                                <button type="submit" class="btn mypage-btn-cancel">
                                                    Yes, Cancel Ticket
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <p class="fs-5 text-muted mt-3 mb-2">
                        Cancel this reservation and release the selected seats.
                        No refund is required because payment has not yet been made.
                    </p>

                    <button type="button" class="btn  btn-danger mypage-btn-cancel mt-2" data-bs-toggle="modal"
                        data-bs-target="#cancelConfirmModal">
                        <i class="fa-solid fa-xmark me-1"></i>
                        Cancel Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('mypage.tickets') }}" class="btn mypage-btn-back text-white border">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to My Tickets
        </a>
    </div>

    {{-- Cancel Confirm Modal --}}
    <div class="modal fade" id="cancelConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content mypage-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>
                        Confirm Cancellation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel your booking for</p>

                    <p class="fw-bold">
                        {{ $reservation->movie->title }}
                    </p>

                    <p class="small text-muted">
                        {{ $reservation->showtime->start_time->format('M d, Y (D) h:i A') }}
                    </p>

                    <p class="small text-muted">
                        The selected seats will become available again.
                        No payment has been made, so no refund is required.
                    </p>

                    <p class="small text-danger mb-0">
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn mypage-btn-back" data-bs-dismiss="modal">
                        Keep Booking
                    </button>
                    <form method="POST" action="{{ route('mypage.cancel.confirm', $reservation->id) }}">
                        @csrf
                        <button type="submit" class="btn mypage-btn-cancel">
                            Yes, Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection