{{-- resources/views/mypage/tickets/index.blade.php --}}
@extends('layouts.mypage')

@section('title', 'My Tickets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-ticket me-2"></i>
            {{ $tab === 'past' ? 'Past Tickets' : 'Upcoming Tickets' }}
        </h2>

        <div class="mypage-tab-switch">
            <a href="{{ route('mypage.tickets', ['tab' => 'upcoming']) }}"
               class="mypage-tab {{ $tab === 'upcoming' ? 'is-active' : '' }}">Upcoming</a>
            <a href="{{ route('mypage.tickets', ['tab' => 'past']) }}"
               class="mypage-tab {{ $tab === 'past' ? 'is-active' : '' }}">Past Tickets</a>
        </div>
    </div>

    <div class="mypage-card p-4">
        @if ($tickets->isEmpty())
            <p class="text-muted mb-0">
                {{ $tab === 'past' ? 'No past tickets yet.' : 'No upcoming tickets yet. Time to book a movie!' }}
            </p>
        @else
            @foreach ($tickets as $reservation)
                <div class="mypage-ticket-row d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex gap-3 align-items-center">
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
                            <div class="fw-bold">{{ $reservation->movie->title }}</div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-calendar"></i>
                                {{ $reservation->showtime->start_time->format('M d, Y (D)') }}
                            </div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-clock"></i>
                                {{ $reservation->showtime->start_time->format('h:i A') }}
                                &middot; Screen {{ $reservation->screen->screen_number ?? '-' }}
                            </div>
                            @if ($reservation->seat_numbers->isNotEmpty())
                                <div class="small text-muted">
                                    <i class="fa-solid fa-chair"></i>
                                    Seat {{ $reservation->seat_numbers->implode(', ') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        @if ($tab === 'upcoming')
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}"
                               class="btn mypage-btn-write-review">
                                View e-Ticket
                            </a>
                        @else
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}"
                               class="btn mypage-btn-back">
                                View e-Ticket
                            </a>
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
        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection{{-- resources/views/mypage/tickets/index.blade.php --}}
@extends('layouts.mypage')

@section('title', 'My Tickets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-ticket me-2"></i>
            {{ $tab === 'past' ? 'Past Tickets' : 'Upcoming Tickets' }}
        </h2>

        <div class="mypage-tab-switch">
            <a href="{{ route('mypage.tickets', ['tab' => 'upcoming']) }}"
               class="mypage-tab {{ $tab === 'upcoming' ? 'is-active' : '' }}">Upcoming</a>
            <a href="{{ route('mypage.tickets', ['tab' => 'past']) }}"
               class="mypage-tab {{ $tab === 'past' ? 'is-active' : '' }}">Past Tickets</a>
        </div>
    </div>

    <div class="mypage-card p-4">
        @if ($tickets->isEmpty())
            <p class="text-muted mb-0">
                {{ $tab === 'past' ? 'No past tickets yet.' : 'No upcoming tickets yet. Time to book a movie!' }}
            </p>
        @else
            @foreach ($tickets as $reservation)
                <div class="mypage-ticket-row d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex gap-3 align-items-center">
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
                            <div class="fw-bold">{{ $reservation->movie->title }}</div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-calendar"></i>
                                {{ $reservation->showtime->start_time->format('M d, Y (D)') }}
                            </div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-clock"></i>
                                {{ $reservation->showtime->start_time->format('h:i A') }}
                                &middot; Screen {{ $reservation->screen->screen_number ?? '-' }}
                            </div>
                            @if ($reservation->seat_numbers->isNotEmpty())
                                <div class="small text-muted">
                                    <i class="fa-solid fa-chair"></i>
                                    Seat {{ $reservation->seat_numbers->implode(', ') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        @if ($tab === 'upcoming')
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}"
                               class="btn mypage-btn-write-review">
                                View e-Ticket
                            </a>
                        @else
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}"
                               class="btn mypage-btn-back">
                                View e-Ticket
                            </a>
                        @endif
                    </div>
                </div>

                @if ($tab === 'upcoming')
                    <div class="text-end pb-1">
                        <small>
                            Can't make it?
                            <a href="{{ route('mypage.cancel.show', $reservation->id) }}"
                               class="mypage-cancel-link">Cancel or Reschedule</a>
                        </small>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    @if ($tickets->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection