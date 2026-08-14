@extends('layouts.mypage')

@section('title', 'Your Movie Tickets')

@section('content')
    <div class="mypage-qr-page text-center">

        <h2 class="mb-1">
            <i class="fa-solid fa-ticket me-2"></i>
            Your Movie Tickets
        </h2>

        <p class="text-muted mb-4">
            {{ $reservation->movie->title }}
        </p>

        {{-- Reservation summary --}}
        <div class="mypage-ticket-details mx-auto p-4 mb-4 text-start">
            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Cinema</span>
                <span>
                    {{ $reservation->cinema->cinema_name ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Showtime</span>
                <span>
                    {{ $reservation->showtime->start_time->format('M d, Y (D) h:i A') }}
                </span>
            </div>

            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Screen</span>
                <span>
                    {{ $reservation->screen->screen_number ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Booking ID</span>
                <span class="fw-semibold">
                    {{ $reservation->reservation_reference ?? $reservation->id }}
                </span>
            </div>

            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Tickets</span>
                <span>
                    {{ $individualTickets->count() }}
                </span>
            </div>
        </div>

        {{-- Individual tickets --}}
        <div class="row justify-content-center g-4">

            @foreach ($individualTickets as $index => $reservationSeat)

                @php
                    $ticket = $reservationSeat->ticket;

                    $screenSeat = $reservationSeat
                        ->showtimeSeat
                        ?->screenSeat;

                    $seatNumber = $screenSeat?->seat_number
                        ?? (
                            ($screenSeat?->seat_row ?? '')
                            . ($screenSeat?->seat_position ?? '')
                        );

                    $ticketNumber = $index + 1;
                @endphp

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="mypage-qr-card p-4 h-100">

                        <div class="mb-3">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                Ticket {{ $ticketNumber }}
                            </span>
                        </div>

                        <h5 class="mb-1">
                            {{ $reservation->movie->title }}
                        </h5>

                        <p class="text-muted mb-3">
                            Seat
                            <span class="fw-bold text-dark">
                                {{ $seatNumber ?: '-' }}
                            </span>
                        </p>

                        {{-- Individual QR --}}
                        <div class="mb-3">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)
                                ->errorCorrection('M')
                                ->generate($ticket->qr_token) !!}
                        </div>

                        @if ($ticket->used_at)
                            <div class="alert alert-secondary py-2 mb-3">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Already Used
                            </div>

                            <div class="small text-muted">
                                Used:
                                {{ $ticket->used_at->format('M d, Y h:i A') }}
                            </div>
                        @else
                            <div class="alert alert-success py-2 mb-3">
                                <i class="fa-solid fa-qrcode me-1"></i>
                                Valid for Entry
                            </div>
                        @endif

                        <div class="small text-muted">
                            Present this QR code at the cinema entrance.
                        </div>

                    </div>
                </div>

            @endforeach

        </div>

        @php
            $canCancel =
            $reservation->payment?->payment_method === 'onsite' &&
            $reservation->payment?->payment_status === 'pending' &&
            now()->lt(
            $reservation->showtime->start_time->copy()->startOfDay()
            );
         @endphp

        <div class="mt-4">
            <a href="{{ route('mypage.tickets') }}" class="btn text-white border-white me-3">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back to My Tickets
            </a>

            @if ($canCancel)
                <a href="{{ route('mypage.cancel.show', $reservation->id) }}" class="btn text-danger border-danger">
                    <i class="fa-solid fa-xmark me-1"></i>
                    Cancel
                </a>
            @endif
        </div>
    </div>

@endsection