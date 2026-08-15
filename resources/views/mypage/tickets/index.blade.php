@extends('layouts.mypage')

@section('title', 'My Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fa-solid fa-ticket me-2"></i>
        @if ($tab === 'upcoming')
            Upcoming Tickets
        @elseif ($tab === 'past')
            Past Tickets
        @elseif ($tab === 'cancelled')
            Cancelled Tickets
        @endif
    </h2>

    <div class="mypage-tab-switch">
        <a href="{{ route('mypage.tickets', ['tab' => 'upcoming']) }}"
            class="mypage-tab {{ $tab === 'upcoming' ? 'is-active' : '' }}">
            Upcoming
        </a>
        <a href="{{ route('mypage.tickets', ['tab' => 'past']) }}"
            class="mypage-tab {{ $tab === 'past' ? 'is-active' : '' }}">
            Past
        </a>
        <a href="{{ route('mypage.tickets', ['tab' => 'cancelled']) }}"
            class="mypage-tab {{ $tab === 'cancelled' ? 'is-active' : '' }}">
            Cancelled
        </a>
    </div>
</div>

@if ($tab === 'upcoming')
    <div class="alert alert-info text-start mb-4">
        <h6 class="fw-bold mb-2">
            <i class="fa-solid fa-circle-info me-1"></i>
            Cancellation Policy
        </h6>

        <p class="mb-0">
            Unpaid Pay On-Site bookings can be cancelled until the day before
            the showtime. Additional conditions may apply to individual ticket
            cancellations.
        </p>
    </div>
@endif

<div class="mypage-card p-4">
    @if ($tickets->isEmpty())
        <p class="text-muted mb-0">
            @if ($tab === 'upcoming')
                No upcoming tickets yet. Time to book a movie!
            @elseif ($tab === 'past')
                No past tickets yet.
            @elseif ($tab === 'cancelled')
                No cancelled tickets.
            @endif
        </p>
    @else
        @foreach ($tickets as $reservation)
            <div class="mypage-ticket-row d-flex justify-content-between align-items-center py-3">
                <div class="d-flex gap-3 align-items-center">
                    @if ($reservation->movie->poster_url)
                        <img src="{{ $reservation->movie->poster_url }}" alt="{{ $reservation->movie->title }}"
                        class="mypage-watched-row-poster">
                    @else
                        <div class="mypage-watched-row-poster mypage-poster-placeholder">
                        <i class="fa-solid fa-film"></i>
                        </div>
                    @endif

                    <div>
                        <div class="fw-bold">
                            {{ $reservation->movie->title }}

                            @if ($reservation->reservation_status === 'cancelled')
                                <span class="badge bg-danger ms-2">
                                    Cancelled
                                </span>
                            @elseif ($reservation->reservation_status === 'expired')
                                <span class="badge bg-secondary ms-2">
                                    Expired
                                </span>
                            @endif
                        </div>

                        <div class="small text-muted">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $reservation->showtime->start_time->format('M d, Y (D)') }}
                        </div>

                        <div class="small text-muted">
                            <i class="fa-solid fa-clock"></i>
                            {{ $reservation->showtime->start_time->format('h:i A') }}
                            &middot;
                            Screen {{ $reservation->screen->screen_number ?? '-' }}
                        </div>

                        @if ($reservation->seat_numbers->isNotEmpty())
                            <div class="small text-muted">
                                <i class="fa-solid fa-chair"></i>
                                Seat {{ $reservation->seat_numbers->implode(', ') }}
                            </div>
                        @endif
                        <div class="small text-muted">
                            <i class="fa-solid fa-receipt"></i>
                            Booking ID:
                            <span class="fw-semibold">
                                {{ $reservation->reservation_reference }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    @if ($tab === 'upcoming')
                        @php
                            $paymentStatus =
                            $reservation->payment?->payment_status ?? 'unpaid';

                            $paymentMethod =
                            $reservation->payment?->payment_method;

                            /*
                            * Cancellation is allowed until 11:59 PM on the day before the screening.
                            * Cancellations are not allowed from midnight on the screening date.
                            */
                            $cancelDeadline =
                            $reservation->showtime->start_time
                            ->copy()
                            ->startOfDay();

                            $canCancel =
                            $reservation->reservation_status === 'confirmed' &&
                            $paymentMethod === 'onsite' &&
                            $paymentStatus === 'pending' &&
                            now()->lt($cancelDeadline);
                        @endphp

                        @if ($paymentStatus === 'paid')
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}" class="btn text-warning border-warning">
                                <i class="fa-solid fa-qrcode me-1"></i>
                                View e-Ticket
                            </a>

                        @elseif ($paymentMethod === 'onsite' && $paymentStatus === 'pending')
                            <div class="text-end">
                                <span class="badge bg-warning text-dark mb-1">
                                    Payment Pending
                                </span>

                                <div class="small text-muted mb-2">
                                    Pay at the cinema counter
                                </div>

                                @if ($canCancel)
                                    <a href="{{ route('mypage.cancel.show', $reservation->id) }}" class="btn btn-sm text-danger border-danger">
                                        <i class="fa-solid fa-xmark me-1"></i>
                                        Cancel
                                    </a>
                                @else
                                    <div class="small text-muted">
                                        Cancellation period has ended
                                    </div>
                                @endif
                            </div>

                        @elseif ($paymentStatus === 'failed')
                            <span class="badge bg-danger">
                                Payment Failed
                            </span>

                        @else
                            <span class="badge bg-secondary">
                                {{ ucfirst($paymentStatus) }}
                            </span>
                        @endif
                    @endif

                    @if ($tab === 'past' && $reservation->reservation_status === 'confirmed')
                        <button type="button" class="btn border-primary text-primary" data-bs-toggle="modal"
                        data-bs-target="#cinemaReviewModal" data-cinema-id="{{ $reservation->cinema_id }}"
                        data-cinema-name="{{ $reservation->cinema->cinema_name }}"
                        data-visited-date="{{ $reservation->showtime->start_time->format('Y-m-d') }}">
                        <i class="fa-solid fa-star me-1"></i> Review Cinema
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

@if ($tickets->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $tickets->links('pagination::bootstrap-5') }}
    </div>
@endif

<div class="mt-4">
    <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

{{-- Cinema Review Modal --}}
<div class="modal fade" id="cinemaReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fa-solid fa-star me-2"></i>Review This Cinema
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="cinemaReviewForm" data-store-url="{{ route('mypage.cinema-reviews.store') }}">
                    @csrf

                    <input type="hidden" id="cinemaId" name="cinema_id">

                    <div class="mb-4">
                        <label class="form-label text-secondary">Cinema</label>
                        <p class="h5" id="cinemaNameDisplay"></p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">When did you visit?</label>
                        <input type="date" class="form-control bg-secondary text-white border-secondary"
                            id="visitedDatePicker" name="visited_at" required>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Rate your experience:</h6>

                        {{-- Image Quality --}}
                        <div class="mb-3 rating-dimension" data-dimension="image_quality">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-film text-info me-2"></i>Image Quality
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="image_quality" class="rating-input">
                        </div>

                        {{-- Sound Quality --}}
                        <div class="mb-3 rating-dimension" data-dimension="sound_quality">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-volume-high text-info me-2"></i>Sound Quality
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="sound_quality" class="rating-input">
                        </div>

                        {{-- Seat Comfort --}}
                        <div class="mb-3 rating-dimension" data-dimension="seat_comfort">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-chair text-info me-2"></i>Seat Comfort
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="seat_comfort" class="rating-input">
                        </div>

                        {{-- Crowding Level --}}
                        <div class="mb-3 rating-dimension" data-dimension="crowding_level">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-people-group text-info me-2"></i>Crowding Level <small>(lower
                                        is better)</small>
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="crowding_level" class="rating-input">
                        </div>

                        {{-- Accessibility --}}
                        <div class="mb-3 rating-dimension" data-dimension="accessibility">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-wheelchair text-info me-2"></i>Accessibility
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="accessibility" class="rating-input">
                        </div>

                        {{-- Service Quality --}}
                        <div class="mb-3 rating-dimension" data-dimension="service_quality">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label mb-0">
                                    <i class="fa-solid fa-handshake text-info me-2"></i>Service Quality
                                </label>
                                <span class="badge bg-secondary rating-value">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="1">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="2">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="3">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="4">★</button>
                                <button type="button" class="btn btn-sm btn-outline-warning star-btn"
                                    data-value="5">★</button>
                            </div>
                            <input type="hidden" name="service_quality" class="rating-input">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Comment (optional)</label>
                        <textarea class="form-control bg-secondary text-white border-secondary" name="comment" rows="3"
                            maxlength="1000"></textarea>
                    </div>

                    <div id="reviewErrorAlert" class="alert alert-danger d-none" role="alert"></div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitReviewBtn">
                            <i class="fa-solid fa-paper-plane me-2"></i>Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
