{{-- resources/views/mypage/tickets/qrcode.blade.php --}}
@extends('layouts.mypage')

@section('title', 'Your Movie Ticket')

@section('content')
    <div class="mypage-qr-page text-center">
        <h2 class="mb-1"><i class="fa-solid fa-ticket me-2"></i>Your Movie Ticket</h2>
        <p class="text-muted mb-4">{{ $reservation->movie->title }}</p>

        <div class="mypage-qr-card d-inline-block p-4 mb-3">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->errorCorrection('M')->generate($reservation->reservation_reference ?? $reservation->id) !!}
        </div>

        <p class="small text-muted mb-4">
            <i class="fa-solid fa-circle-info me-1"></i>
            Show this QR code at the entrance
        </p>

        <div class="mypage-ticket-details mx-auto p-4 mb-4">
            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Showtime</span>
                <span>{{ $reservation->showtime->start_time->format('M d, Y (D) h:i A') }}</span>
            </div>
            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Screen</span>
                <span>{{ $reservation->screen->screen_number ?? '-' }}</span>
            </div>
            @if ($reservation->seat_numbers->isNotEmpty())
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Seat</span>
                    <span>{{ $reservation->seat_numbers->implode(', ') }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between py-1">
                <span class="text-muted">Booking ID:</span>
                <span>{{ $reservation->reservation_reference ?? $reservation->id }}</span>
            </div>
        </div>
        <div class="d-l"></div>

        <a href="{{ route('mypage.tickets') }}" class="btn text-white border-white me-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to My Tickets
        </a>
        <button type="button" class="btn text-danger border-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
            <i class="fa-solid fa-xmark"></i>Cancel
        </button>
    </div>

    {{-- Candel Modal --}}
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mypage-modal">
                <div class="modal-header">
                    <h5>
                        ⚠️Cancel Reservation⚠️
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to cancel this reservation?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn border-dark" data-bs-dismiss="modal">
                        Back
                    </button>

                    <form action="{{ route('mypage.reservations.cancel', $reservation) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn  text-danger border-danger">
                            Cancel Reservation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
