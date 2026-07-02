{{-- resources/views/mypage/dashboard.blade.php --}}
@extends('layouts.mypage')

@section('title', 'Dashboard')

@section('content')
    <div class="mypage-welcome mb-4">
        <h2>Welcome back, <span class="text-warning">{{ $user->first_name }}</span> 🍿</h2>
        <p class="text-muted">Enjoy the perfect movie experience.</p>
    </div>

    {{-- Upcoming Tickets --}}
    <div class="mypage-card mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-ticket me-2"></i>Upcoming Tickets</h5>
            <a href="#" class="mypage-view-all">View All &rarr;</a>
        </div>

        @if ($upcomingTickets->isEmpty())
            <p class="text-muted mb-0">No upcoming tickets yet. Time to book a movie!</p>
        @else
            <div class="row g-3">
                @foreach ($upcomingTickets as $reservation)
                    <div class="col-md-6">
                        <div class="mypage-ticket-card d-flex gap-3 p-3">
                            <img src="{{ $reservation->movie->poster_url }}"
                                 alt="{{ $reservation->movie->title }}"
                                 class="mypage-ticket-poster">
                            <div>
                                <div class="fw-bold">{{ $reservation->movie->title }}</div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-calendar"></i>
                                    {{ $reservation->showtime->start_time->format('M d, Y (D)') }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-clock"></i>
                                    {{ $reservation->showtime->start_time->format('h:i A') }}
                                </div>
                                <div class="small text-muted">
                                    Screen {{ $reservation->screen->screen_number ?? $reservation->screen_id }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recently Watched --}}
    <div class="mypage-card mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-film me-2"></i>Recently Watched</h5>
            <a href="{{ route('mypage.movies-watched') }}" class="mypage-view-all">View All &rarr;</a>
        </div>

        @if ($recentlyWatched->isEmpty())
            <p class="text-muted mb-0">No watched movies yet.</p>
        @else
            <div class="row g-3">
                @foreach ($recentlyWatched as $reservation)
                    <div class="col-md-4 text-center">
                        <img src="{{ $reservation->movie->poster_url }}"
                             alt="{{ $reservation->movie->title }}"
                             class="mypage-watched-poster mb-2">
                        <div class="fw-bold">{{ $reservation->movie->title }}</div>
                        <div class="small text-muted">
                            Watched on {{ $reservation->showtime->start_time->format('M d, Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- My Reviews --}}
    <div class="mypage-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-star me-2"></i>My Reviews</h5>
            <a href="#" class="mypage-view-all">View All &rarr;</a>
        </div>

        @if ($myReviews->isEmpty())
            <p class="text-muted mb-0">You haven't written any reviews yet.</p>
        @else
            @foreach ($myReviews as $review)
                <div class="mypage-review-row d-flex justify-content-between align-items-start py-2">
                    <div>
                        <div class="fw-bold">{{ $review->movie->title }}</div>
                        <div class="mypage-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="small text-muted mb-0">{{ Str::limit($review->body, 100) }}</p>
                    </div>
                    <div class="small text-muted text-nowrap">
                        {{ $review->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection