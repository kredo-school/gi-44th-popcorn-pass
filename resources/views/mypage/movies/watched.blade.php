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
                            <button type="button" class="btn mypage-btn-write-review"
                                    data-bs-toggle="modal"
                                    data-bs-target="#reviewModal-{{ $reservation->id }}">
                                Write a Review
                            </button>

                            {{-- Review Modal --}}
                            <div class="modal fade" id="reviewModal-{{ $reservation->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content mypage-modal">
                                        <form method="POST" action="{{ route('mypage.reviews.store') }}">
                                            @csrf
                                            <input type="hidden" name="movie_id" value="{{ $reservation->movie_id }}">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Review: {{ $reservation->movie->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">Your Rating</label>
                                                <div class="mypage-star-input mb-3">
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <input type="radio" name="rating" id="star{{ $i }}-{{ $reservation->id }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                                                        <label for="star{{ $i }}-{{ $reservation->id }}"><i class="fa-solid fa-star"></i></label>
                                                    @endfor
                                                </div>

                                                <label class="form-label">Your Review</label>
                                                <textarea name="body" class="form-control" rows="4" required maxlength="2000" placeholder="What did you think of this movie?"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn mypage-btn-back" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn mypage-btn-write-review">Submit Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection