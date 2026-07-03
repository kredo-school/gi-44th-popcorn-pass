@extends('layouts.app')
@section('title', 'Movie Review')
@section('content')


<div class="review-page">

    <h2 class="text-center text-white fw-bold mb-4">Movie Reviews</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>        
    @endif

    <div class="row">

        {{-- Left: title & poster --}}
        <div class="col-lg-3 text-center">
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="img-fluid mb-3" style="max-width: 200px">

            @auth
              <a href="{{ route('reviews.create', $movie->id) }}" class="review-write-btn">
                Write a Review
            </a>
            @endauth
        </div>

        {{-- Right: Reviews --}}
        <div class="col-lg-9">
            {{-- Movie title & Rating --}}
            <h3 class="text-white fw-bold mb-2">{{ strtoupper($movie->title) }}</h3>

            <div class="d-flex align-items-center mb-3">
                @for ($i = 1; $i <= 5; $i++) @if ($i <=round($averageRating)) <i class="fa-solid fa-star text-warning"></i>
                    @else
                        <i class="fa-regular fa-star text-warning"></i>
                    @endif
                @endfor
                    <span class="text-white fw-bold ms-2">{{ number_format($averageRating, 1) }}/5</span>
                    <span class="text-warning ms-2">({{ number_format($totalReviews) }} reviews)</span>
            </div>
    

            {{-- Review List --}}
            @forelse ($reviews as $review)
                <div class="review-card mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-solid fa-user-circle fa-2x"></i>
                        <span class="fw-bold me-3 ms-2">{{ $review->user->name }}</span>
                        <div>
                            @for ($i = 1; $i <= 5; $i++) @if ($i <=$review->rating)
                                    <i class="fa-solid fa-star text-warning"></i>
                                @else
                                    <i class="fa-regular fa-star text-warning"></i>
                                @endif
                                @endfor
                        </div>
                    </div>
                    <p class="mb-0 review-body">{{ Str::limit($review->body, 100) }}</p>
                    <div class="text-end">
                        <a href="{{ route('reviews.show', [$movie->id, $review->id]) }}" class="text-muted">&gt;</a>
                    </div>
                </div>
            @empty
                <p class="text-white">No reviews yet.</p>
            @endforelse
        </div>

    </div>

    {{-- Back Button !!!!!UPDATE LATER!!!!--}}
    <div class="mb-3">
        <a href="#" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </a>
    </div>

</div>


    
@endsection