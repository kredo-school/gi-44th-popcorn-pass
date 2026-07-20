@extends('layouts.app')
@section('title', 'Movie Review')
@section('content')

    <div class="review-page px-5">

        {{-- Title --}}
        <div class="review-title-wrap text-center mb-5">
            <h2 class="review-main-title">— Movie Reviews —</h2>
            <div class="review-title-deco">◇ ✦ ◇</div>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="row align-items-start px-4">

            {{-- Left --}}
            <div class="col-lg-4 text-center">
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="review-poster img-fluid rounded mb-4">
            </div>

            {{-- Right --}}
            <div class="col-lg-8">

                <h3 class="review-movie-title mb-2">{{ strtoupper($movie->title) }}</h3>

                {{-- Stars --}}
                <div class="d-flex align-items-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($averageRating))
                            <i class="fa-solid fa-star review-star-lg"></i>
                        @else
                            <i class="fa-regular fa-star review-star-lg"></i>
                        @endif
                    @endfor
                    <span class="review-avg-score ms-2">{{ number_format($averageRating, 1) }} / 5</span>
                    <span class="review-total-count ms-2">({{ number_format($totalReviews) }} reviews)</span>
                </div>

                {{-- Review List --}}
                <div class="d-flex justify-content-end mb-4">
                    @auth
                        <a href="{{ route('reviews.create', $movie->id) }}" class="review-write-btn">
                            <i class="fa-solid fa-pencil me-2"></i>Write a Review
                        </a>
                    @endauth
                </div>

                <div class="">
                    @forelse ($reviews as $review)
                        <div class="review-card mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="review-avatar-circle me-3">
                                    @if ($review->user->avatar)
                                        <img src="{{ $review->user->avatar }}" alt="{{ $review->user->username }}" class="review-avatar-img">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <span class="review-username fw-bold me-auto">{{ $review->user->username }}</span>
                                <div class="d-flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="fa-solid fa-star review-star-sm"></i>
                                        @else
                                            <i class="fa-regular fa-star review-star-sm"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <hr class="review-divider">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0 review-body">{!! nl2br(e($review->body)) !!}</p>
                                <a href="{{ route('reviews.show', [$movie->id, $review->id]) }}" class="review-arrow">
                                    ›
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-white">No reviews yet.</p>
                    @endforelse

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                </div>

            </div>

        </div>

        {{-- Back Button --}}
        <button type="button" class="back-btn ms-5" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </button>

    </div>

@endsection
