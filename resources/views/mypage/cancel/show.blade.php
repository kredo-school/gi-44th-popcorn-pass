{{-- resources/views/mypage/cancel/show.blade.php --}}
@extends('layouts.mypage')

@section('title', 'Cancel or Reschedule')

@section('content')
    <div class="mb-4">
        <h2><i class="fa-solid fa-calendar-xmark me-2"></i>Cancel or Reschedule</h2>
        <p class="text-muted">Manage your booking for <strong class="text-white">{{ $reservation->movie->title }}</strong></p>
    </div>

    {{-- Booking Summary --}}
    <div class="mypage-card p-4 mb-4">
        <h5 class="mb-3">Booking Summary</h5>
        <div class="d-flex gap-3 align-items-start">
            @if ($reservation->movie->poster_url)
                <img src="{{ $reservation->movie->poster_url }}"
                     alt="{{ $reservation->movie->title }}"
                     class="mypage-watched-row-poster">
            @else
                <div class="mypage-watched-row-poster mypage-poster-placeholder">
                    <i class="fa-solid fa-film"></i>
                </div>
            @endif
            <div>
                <div class="fw-bold mb-1">{{ $reservation->movie->title }}</div>
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
                <div class="small text-muted mt-1">
                    <i class="fa-solid fa-tag me-1"></i>
                    Total: ¥{{ number_format($reservation->final_amount) }}
                </div>
            </div>
        </div>
    </div>

    {{-- 2 action cards --}}
    <div class="row g-3 mb-4">
        {{-- Cancel --}}
        <div class="col-md-6">
            <div class="mypage-card p-4 h-100 d-flex flex-column">
                <h5 class="mb-2"><i class="fa-solid fa-xmark me-2 text-danger"></i>Cancel Booking</h5>
                <p class="small text-muted flex-grow-1">
                    Cancel this reservation. The booking will be marked as cancelled and a refund will be processed by our team.
                </p>
                <button type="button" class="btn mypage-btn-cancel w-100 mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#cancelConfirmModal">
                    Cancel Booking
                </button>
            </div>
        </div>

        {{-- Reschedule (placeholder) --}}
        <div class="col-md-6">
            <div class="mypage-card p-4 h-100 d-flex flex-column">
                <h5 class="mb-2"><i class="fa-solid fa-rotate me-2" style="color: var(--mypage-gold)"></i>Reschedule Booking</h5>
                <p class="small text-muted flex-grow-1">
                    Want to see the movie at a different time? Contact our support team to reschedule your booking.
                </p>
                <a href="mailto:support@popcornpass.test" class="btn mypage-btn-back w-100 mt-2">
                    Contact Support
                </a>
            </div>
        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('mypage.tickets') }}" class="btn mypage-btn-back">
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
                    <p class="fw-bold">{{ $reservation->movie->title }}</p>
                    <p class="small text-muted">
                        {{ $reservation->showtime->start_time->format('M d, Y (D) h:i A') }}
                    </p>
                    <p class="small text-muted">This action cannot be undone.</p>
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