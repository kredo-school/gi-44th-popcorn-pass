{{-- resources/views/mypage/tickets/index.blade.php --}}
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

                                @if ($tab === 'cancelled')
                                    <span class="badge bg-danger ms-2">
                                        Cancelled
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
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        @if ($tab !== 'cancelled')
                            <a href="{{ route('mypage.tickets.qrcode', $reservation->id) }}"
                                class="btn {{ $tab === 'upcoming' ? 'text-warning border-warning' : 'border-white text-white' }}">
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
        <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
    @endsection
