@extends('layouts.app')
@section('title', 'Review Detail')
@section('content')

<div class="review-page">

    <div class="row align-items-start">

        {{-- Left: Poster --}}
        <div class="col-lg-4 text-center">
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="img-fluid rounded"
                style="max-width: 200px;">
        </div>

        {{-- Right: Review Detail --}}
        <div class="col-lg-6">
            <h3 class="text-white fw-bold mb-4">{{ strtoupper($movie->title) }}</h3>

            <div class="review-form-card">

                {{-- User --}}
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-user fa-2x me-2 text-warning"></i>
                    <span class="fw-bold fs-5">{{ $review->user->name }}</span>
                </div>

                {{-- Rating --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Rating</label>
                    <div class="d-flex gap-1">
                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$review->rating)
                            <i class="fa-solid fa-star text-warning fs-4"></i>
                            @else
                            <i class="fa-regular fa-star text-warning fs-4"></i>
                            @endif
                            @endfor
                    </div>
                </div>

                {{-- Comment --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Comment</label>
                    <p class="review-show-body">{{ $review->body }}</p>
                </div>

                {{-- Edit Button (own review only) --}}
                @auth
                @if (Auth::id() === $review->user_id)
                <div class="text-center">
                    <a href="{{ route('reviews.index', $movie->id) }}" class="review-cancel-btn">
                        Back
                    </a>
                    <a href="{{ route('reviews.edit', [$movie->id, $review->id]) }}" class="review-edit-btn">
                        Edit
                    </a>
                </div>
                @endif
                @endauth

            </div>
        </div>

    </div>

</div>

@endsection