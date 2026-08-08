@extends('layouts.mypage')

@section('title', 'Write a Review')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-pen me-2"></i>Write a Review</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mypage-card p-4 review-form-card">
        <div class="d-flex gap-3 align-items-center mb-4">
            @if ($movie->poster_url)
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="review-form-poster">
            @endif
            <div>
                <div class="fw-bold fs-5">{{ $movie->title }}</div>
            </div>
        </div>

        @if ($alreadyReviewed)
            <p class="text-muted mb-0">You've already reviewed this movie. Thank you!</p>
            <a href="{{ route('mypage.reviews-written') }}" class="btn mypage-btn-back mt-3">
                View Your Reviews
            </a>
        @else
            <form method="POST" action="{{ route('mypage.reviews.store') }}">
                @csrf
                <input type="hidden" name="movie_id" value="{{ $movie->id }}">

                <label class="form-label">Your Rating</label>
                <div class="mypage-star-input mb-3">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                        <label for="star{{ $i }}"><i class="fa-solid fa-star"></i></label>
                    @endfor
                </div>

                <label class="form-label">Your Review</label>
                <textarea name="body" class="form-control mb-4" rows="5" required maxlength="2000"
                          placeholder="What did you think of this movie?"></textarea>

                <button type="submit" class="btn mypage-btn-write-review px-4">Submit Review</button>
                <a href="{{ route('mypage.movies-watched') }}" class="btn mypage-btn-back ms-2">Cancel</a>
            </form>
        @endif
    </div>
@endsection