@extends('layouts.app')
@section('title', 'Review Detail')
@section('content')

<div class="review-page">

    {{-- Title --}}
    <div class="review-title-wrap text-center mb-5">
        <h2 class="review-main-title">— Movie reviews —</h2>
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

        {{-- Right: Review Detail --}}
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
                    <span class="fw-bold fs-5">{{ $review->user->username }}</span>
                </div>

                <hr class="review-divider">

                {{-- Rating --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Rating</label>
                    <div class="d-flex gap-1">
                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$review->rating)
                            <i class="fa-solid fa-star review-star-sm"></i>
                            @else
                            <i class="fa-regular fa-star review-star-sm"></i>
                            @endif
                            @endfor
                    </div>
                </div>

                {{-- Comment --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Comment</label>
                    <p class="review-show-body">{!! nl2br(e($review->body)) !!}</p>
                </div>

                {{-- Edit & Delete Button (own review only) --}}
                @auth
                @if (Auth::id() === $review->user_id)
                <div class="d-flex justify-content-center gap-3">
                    <form action="{{ route('reviews.destroy', [$movie->id, $review->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="review-delete-btn"
                            onclick="return confirm('Are you sure you want to delete this review?')">
                            Delete
                        </button>
                    </form>
                    <a href="{{ route('reviews.edit', [$movie->id, $review->id]) }}" class="review-edit-btn">
                        Edit
                    </a>
                </div>
                @endif
                @endauth

            </div>
        </div>

    </div>

    <a href="{{ route('reviews.index', $movie->id) }}" class="back-btn text-decoration-none">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </a>

</div>

@endsection