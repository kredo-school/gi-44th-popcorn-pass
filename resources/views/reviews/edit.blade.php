@extends('layouts.app')
@section('title', 'Edit Review')
@section('content')

<div class="review-page">

    {{-- Title --}}
    <div class="review-title-wrap text-center mb-5">
        <h2 class="review-main-title">— Reviews —</h2>
        <div class="review-title-deco">◇ ✦ ◇</div>
    </div>

    <div class="row align-items-start px-4">

        {{-- Left --}}
        <div class="col-lg-6 text-center">
            {{-- Poster --}}
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="review-poster img-fluid rounded mb-0">
        
            {{-- Movie Info --}}
            <div class="review-movie-info-card mx-auto">
        
                <div class="review-info-item">
                    <span class="review-info-label">
                        <i class="fa-solid fa-film me-2"></i>Genre
                    </span>
                    <span class="review-info-value">{{ $movie->genre->title }}</span>
                </div>
        
                <div class="review-info-item">
                    <span class="review-info-label">
                        <i class="fa-regular fa-clock me-2"></i>Runtime
                    </span>
                    <span class="review-info-value">{{ $movie->duration }} min</span>
                </div>
        
                <div class="review-info-item">
                    <span class="review-info-label">
                        <i class="fa-regular fa-calendar me-2"></i>Released Date
                    </span>
                    <span class="review-info-value">
                        {{ $movie->released_date->format('M d, Y') }}
                    </span>
                </div>
        
                <div class="review-info-item">
                    <span class="review-info-label">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>Age Rating
                    </span>
                    <span class="review-info-value">{{ $movie->ageRating->title }}</span>
                </div>
        
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="col-lg-6">
            <h3 class="review-movie-title mb-4">{{ strtoupper($movie->title) }}</h3>

            <div class="review-form-card">

                {{-- User --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="review-avatar-circle me-3">
                        @if ($review->user->avatar)
                            <img src="{{ $review->user->avatar }}" alt="{{ $review->user->username }}" class="review-avatar-img">
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>
                    <span class="fw-bold fs-5">{{ Auth::user()->username }}</span>
                </div>

                <hr class="review-divider">

                <form action="{{ route('reviews.update', [$movie->id, $review->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Rating --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rating</label>
                        <div class="star-rating">
                            @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ (old('rating',
                                $review->rating) == $i) ? 'checked' : '' }}>
                            <label for="star{{ $i }}">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            @endfor
                        </div>
                        @error('rating')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Comment --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Comment (Optional)</label>
                        <textarea name="body" class="form-control review-textarea" placeholder="Enter your comment..."
                            rows="5">{{ old('body', $review->body) }}</textarea>
                        @error('body')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('reviews.show', [$movie->id, $review->id]) }}" class="review-cancel-btn">Cancel</a>
                        <button type="submit" class="review-update-btn">Update</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

@endsection