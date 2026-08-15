@extends('layouts.mypage')

@section('title', 'Cancel')

@section('content')
    <div class="mb-4">
        <h2><i class="fa-solid fa-calendar-xmark me-2"></i>Cancel</h2>
        <p class="text-muted">Manage your booking for <strong class="text-white">{{ $reservation->movie->title }}</strong></p>
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