@extends('layouts.app')

@section('content')
    <div class="reservation-page" style="background-image: url('{{ asset('images/layouts/background.png') }}');">
        <div class="text-center">
            <div class="release-date">
                Movie Detail
            </div>

            <div class="release-sub"></div>
        </div>

        <div class="container movie-container">
            <div class="mt-5 row text-white justify-content-center">
                <div class="col-12 col-md-5 text-center text-md-end mb-4 mb-md-0">
                    <img src="{{ $movie->poster_url ?: asset('images/no-poster.png') }}" alt="{{ $movie->title }}"
                        class="release-img" onerror="this.onerror=null; this.src='{{ asset('images/no-poster.png') }}';">
                </div>

                <div class="col-12 col-md-5 blue-background">
                    <div class="pt-3">
                        <div class="text-center">
                            <h1>{{ $movie->title }}</h1>

                            <div class="mt-1">
                                🎬 {{ optional($movie->ageRating)->title ?? 'Not Rated' }}
                            </div>
                        </div>
                    </div>

                    <div class="synopsis-box mt-4 mb-3">
                        {{ $movie->synopsis ?: 'No synopsis available.' }}
                    </div>

                    <div class="row p-3">
                        <div class="col-4 fw-bold">
                            Review Average
                        </div>

                        <div class="col-8">
                            <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}" class="review-summary-link">

                                <span class="review-star">⭐</span>

                                <span class="review-avg-score">
                                    {{ number_format((float) ($averageRating ?? 0), 1) }} / 5
                                </span>

                                <span class="review-total-count">
                                    ({{ number_format((int) ($totalReviews ?? 0)) }} reviews)
                                </span>

                                <span class="review-view-text">
                                    View Reviews
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            </a>
                        </div>

                        <div class="col-4 fw-bold">
                            Genre
                        </div>

                        <div class="col-8">
                            {{ $movie->genres?->pluck('title')->filter()->join(', ') ?: 'Not available' }}
                        </div>

                        <div class="col-4 fw-bold">
                            RELEASE DATE
                        </div>

                        <div class="col-8">
                            @if ($movie->released_date)
                                {{ $movie->released_date->format('Y-m-d') }}
                            @else
                                Not available
                            @endif
                        </div>

                        <div class="col-4 fw-bold">
                            END DATE
                        </div>

                        <div class="col-8">
                            @if ($movie->end_date)
                                {{ $movie->end_date->format('Y-m-d') }}
                            @else
                                Not available
                            @endif
                        </div>

                        <div class="col-4 fw-bold">
                            RUN TIME
                        </div>

                        <div class="col-8">
                            {{ $movie->duration ? $movie->duration . ' min' : 'Not available' }}
                        </div>

                        <div class="col-4 fw-bold">
                            DIRECTOR
                        </div>

                        <div class="col-8">
                            {{ $movie->director ?: 'Not available' }}
                        </div>

                        <div class="col-4 fw-bold">
                            CAST
                        </div>

                        <div class="col-8">
                            @php
                                $castMembers = $movie->cast;

                                if (is_string($castMembers)) {
                                    $decoded = json_decode($castMembers, true);

                                    if (is_array($decoded)) {
                                        $castMembers = $decoded;
                                    } else {
                                        $castMembers = [$castMembers];
                                    }
                                }

                                $castMembers = is_array($castMembers) ? $castMembers : [];
                            @endphp

                            @forelse ($castMembers as $castMember)
                                <div class="row">
                                    <div class="col-12">
                                        {{ $castMember }}
                                    </div>
                                </div>
                            @empty
                                Not available
                            @endforelse
                        </div>
                    </div>

                    @if ($movie->trailer_url)
                        <a href="{{ $movie->trailer_url }}" target="_blank" rel="noopener noreferrer" class="text-trailer">
                            Watch the trailer
                        </a>
                    @else
                        <span class="text-muted">
                            Trailer unavailable
                        </span>
                    @endif

                    <div class="text-center mt-3 mb-3">
                        @if ($showtime)
                            <a
                                href="{{ route('reservations.showtime.selection', [
                                    'movie' => $showtime->movie->id,
                                ]) }}" class="text-decoration-none">
                                <button type="button" class="book-btn mt-2 w-75">
                                    BOOK NOW
                                </button>
                            </a>
                        @else
                            <button type="button" class="book-btn mt-2 w-75" disabled>
                                NO SHOWTIMES AVAILABLE
                            </button>

                            <p class="text-muted small mt-2 mb-0">
                                There are currently no available showtimes for this movie.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Discussion Section -->
        <div class="container community-discussion mt-5 mb-5">
            <div class="text-white">
                <h2 class="mb-4">
                    💬 Community Discussion
                </h2>

                @auth
                    <div class="post-form mb-4">
                        <form id="newPostForm">
                            <div class="form-group mb-3">
                                <input type="text" id="postTitle" class="form-control" placeholder="Post title..."
                                    maxlength="255" required>
                            </div>

                            <div class="form-group mb-3">
                                <textarea id="postBody" class="form-control" placeholder="Share your thoughts... (No spoilers!)" rows="4"
                                    maxlength="2000" required></textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="postSpoilerFlag">

                                <label class="form-check-label" for="postSpoilerFlag">
                                    ⚠️ This post contains spoilers
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Post
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <a href="{{ route('login') }}">
                            Log in
                        </a>
                        to post in the community discussion.
                    </div>
                @endauth

                <div id="postsList" class="posts-list">
                    <p class="text-muted">
                        Loading posts...
                    </p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-5">
            <a href="{{ url('/home') }}" class="back-btn ms-5 t">
                <i class="fa-solid fa-arrow-left"></i> HOME
            </a>
        </div>
    </div>

    <meta name="movie-id" content="{{ $movie->id }}">
@endsection
