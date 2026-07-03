@extends('layouts.app')
@section('title', 'Write a Review')
@section('content')

<div class="review-page">

    <div class="row align-items-start">

        {{-- Left: Poster --}}
        <div class="col-lg-4 text-center">
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="img-fluid rounded"
                style="max-width: 200px;">
        </div>

        {{-- Right: Form --}}
        <div class="col-lg-6">
            <h3 class="text-white fw-bold mb-4">{{ strtoupper($movie->title) }}</h3>

            <div class="review-form-card">

                {{-- User --}}
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-user fa-2x me-2 text-warning"></i>
                    <span class="fw-bold fs-5">{{ Auth::user()->name }}</span>
                </div>

                <form action="{{ route('reviews.store', $movie->id) }}" method="POST">
                    @csrf

                    {{-- Rating --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rating</label>
                        <div class="star-rating">
                            @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating')==$i
                                ? 'checked' : '' }}>
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
                        <label class="form-label fw-bold">Comment</label>
                        <textarea name="body" class="form-control review-textarea" placeholder="Enter your comment..."
                            rows="5">{{ old('body') }}</textarea>
                        @error('body')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <a href="{{ route('reviews.index', $movie->id) }}" class="review-cancel-btn">
                            Cancel
                        </a>
                        <button type="submit" class="review-save-btn">Save</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

@endsection