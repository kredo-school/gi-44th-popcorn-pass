@extends('layouts.mypage')

@section('title', 'Booking Cancelled')

@section('content')
    <div class="mypage-cancel-complete text-center py-5">
        <div class="mb-4">
            <i class="fa-solid fa-circle-check fa-4x" style="color: var(--mypage-gold)"></i>
        </div>

        <h2 class="mb-2">Booking Cancelled</h2>
        <p class="text-muted mb-4">Your booking has been successfully cancelled.</p>

        <div class="mypage-ticket-details mx-auto p-4 mb-4">
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Movie</span>
                <span class="fw-bold">{{ $reservation->movie->title }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Date</span>
                <span>{{ $reservation->showtime->start_time->format('M d, Y (D)') }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Reference</span>
                <span>{{ $reservation->reservation_reference ?? $reservation->id }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Refund Amount</span>
                <span class="fw-bold" style="color: var(--mypage-gold)">
                    ¥{{ number_format($reservation->final_amount) }}
                </span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Cancelled At</span>
                <span>{{ $reservation->cancelled_at?->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        <p class="small text-muted mb-4">
            Refunds are typically processed within 3-5 business days.
        </p>

        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-write-review px-4">
            <i class="fa-solid fa-house me-2"></i>Back to My Page
        </a>
    </div>
@endsection