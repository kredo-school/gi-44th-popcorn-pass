@extends('layouts.app')
@section('content')
    <div class="reservation-page " style="background-image: url('{{ asset('images/layouts/background.png') }}');">

        <div class="text-center">
            <div class="release-date">
                Movie Detail
            </div>

            <div class="release-sub">

            </div>
        </div>
        <div class="container movie-container ">
            <div class="mt-5 row text-white ">
                <div class="col-5 text-end ">
                    <img src="{{ $movie->poster_url }}" alt="movie-title" class="release-img">
                </div>
                <div class="col-5 blue-background ">
                    <div class="pt-3">
                        <div class="text-center">
                            <h1>{{ $movie->title }}</h1>

                            <div class="mt-1">
                                🎬 {{ $movie->ageRating->title }}
                            </div>
                        </div>



                    </div>
                    <div class="synopsis-box mt-4 mb-3">
                        {{ $movie->synopsis }}
                    </div>
                    <div class="row p-3">
                        <div class="col-4 fw-bold">Review Average</div>
                        <div class="col-8">
                            <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                                class="text-decoration-none text-white text-small">

                                ⭐
                                <span class=" ms-2">
                                    {{ number_format($averageRating, 1) }} / 5
                                </span>

                                <span class="review-total-count ms-2">
                                    ({{ number_format($totalReviews) }} reviews)
                                </span>

                            </a>
                        </div>




                        <div class="col-4 fw-bold">Genre</div>
                        <div class="col-8 ">
                            {{ $movie->genres->pluck('title')->join(', ') }}
                        </div>
                        <div class="col-4 fw-bold">RELEASE DATE</div>
                        <div class="col-8">
                            {{ \Carbon\Carbon::parse($movie->released_date)->format('Y-m-d') }}
                        </div>
                        <div class="col-4 fw-bold">END DATE</div>
                        <div class="col-8">
                            {{ \Carbon\Carbon::parse($movie->end_date)->format('Y-m-d') }}
                        </div>

                        <div class="col-4 fw-bold">RUN TIME</div>
                        <div class="col-8">{{ $movie->duration }} min</div>

                        <div class="col-4 fw-bold">DIRECTOR</div>
                        <div class="col-8">{{ $movie->director }}</div>

                        <div class="col-4 fw-bold">CAST</div>
                        <div class="col-8">
                            <div class="row">
                            @foreach ($movie->cast ?? [] as $cast)
                                <div class="col-6">{{ $cast }}</div>
                            @endforeach
                            </div>
                            
                        </div>
                    </div>
                    <a href="{{ $movie->trailer_url }}" target="_blank" class="text-trailer">
                        Watch the trailer
                    </a>
                    <div class="text-center mt-3 mb-3">
                        <a href="{{ route('reservations.seat-selection', [
                            'showtime' => $showtime->id,
                            'new' => 1,
                        ]) }}" class="text-decoration-none">
                            <button class="book-btn mt-2 w-75">BOOK NOW</button>
                        </a>
                    </div>


                </div>
            </div>
        </div>

        <!-- Community Discussion Section -->
        <div class="container community-discussion mt-5 mb-5">
            <div class="text-white">
                <h2 class="mb-4">💬 Community Discussion</h2>
                
                <!-- New Post Form -->
                @auth
                <div class="post-form mb-4">
                    <form id="newPostForm">
                        <div class="form-group mb-3">
                            <input type="text" id="postTitle" class="form-control" placeholder="Post title..." maxlength="255" required>
                        </div>
                        <div class="form-group mb-3">
                            <textarea id="postBody" class="form-control" placeholder="Share your thoughts... (No spoilers!)" rows="4" maxlength="2000" required></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="postSpoilerFlag">
                            <label class="form-check-label" for="postSpoilerFlag">
                                ⚠️ This post contains spoilers
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
                @else
                <div class="alert alert-info mb-4">
                    <a href="{{ route('login') }}">Log in</a> to post in the community discussion.
                </div>
                @endauth

                <!-- Posts List -->
                <div id="postsList" class="posts-list">
                    <p class="text-muted">Loading posts...</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-5">
            <button type="button" class="back-btn ms-5" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> BACK
            </button>
        </div>

    </div>

    <meta name="movie-id" content="{{ $movie->id }}">
@endsection