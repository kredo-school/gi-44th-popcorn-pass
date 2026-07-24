{{-- resources/views/mypage/movies/watched.blade.php --}}
@extends('layouts.mypage')

@section('title', 'Movies Watched')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-film me-2"></i>Movies Watched</h2>

        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="sort" class="small text-muted mb-0">Sort by:</label>
            <select id="sort" name="sort" class="form-select form-select-sm mypage-sort-select" onchange="this.form.submit()">
                <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>Most Recent</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
            </select>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mypage-card p-4">
        @if ($watched->isEmpty())
            <p class="text-muted mb-0">You haven't watched any movies yet.</p>
        @else
            @foreach ($watched as $reservation)
                @php
                    $alreadyReviewed = in_array($reservation->movie_id, $reviewedMovieIds);
                    // review_average is on a 0-10 scale in the DB; normalize to 0-5 for star display
                    $avg = round((($reservation->movie->review_average ?? 0) / 10) * 5);
                @endphp
                <div class="mypage-watched-row d-flex justify-content-between align-items-center py-3">
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
                            <div class="small text-muted mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                Watched on {{ $reservation->showtime->start_time->format('M d, Y') }}
                            </div>
                            <div class="mypage-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $avg ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div>
                        @if ($alreadyReviewed)
                            <span class="btn mypage-btn-reviewed disabled">
                                <i class="fa-solid fa-check me-1"></i>Reviewed
                            </span>
                        @else
                            <form method="POST" action="{{ route('mypage.movies-watched.send-review-email', $reservation->id) }}">
                                @csrf
                                <button type="submit" class="btn text-warning border-warning">
                                    <i class="fa-solid fa-envelope me-1"></i>Email Me a Review Link
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if ($watched->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $watched->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection