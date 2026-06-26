@extends('layouts.app')

@section('content')
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active">
            </button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1">
            </button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2">
            </button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3">
            </button>
        </div>

        <div class="carousel-inner">
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            {{-- Seatimage --}}
            <div class="carousel-item active">
                <img src="{{ asset('images/seatimage.png') }}" class="hero-image-welcome">
            </div>

            {{-- Screen --}}
            <div class="carousel-item ">
                <img src="{{ asset('images/screen.png') }}" class="hero-image-welcome">
            </div>

            {{-- COMING SOON --}}
            <div class="carousel-item">

                <img src="{{ asset('images/king.png') }}" class="hero-image">

                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <span class="hero-tag">
                        COMING SOON
                    </span>
                    <h1>
                        UPCOMING<br>
                        BLOCKBUSTERS
                    </h1>
                    <p>
                        Discover the most anticipated movies.
                    </p>
                    <a href="#" class="btn-book">
                        VIEW MORE →
                    </a>
                </div>
            </div>

            {{-- TOP RANKING --}}
            <div class="carousel-item">

                <img src="{{ asset('images/king2.png') }}" class="hero-image">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <span class="hero-tag" style="color:#ff4040;">
                        TOP RANKING
                    </span>
                    <h1>
                        No.1 MOVIE<br>
                        OF THE WEEK
                    </h1>
                    <p>
                        Most watched by our audience.
                    </p>
                    <a href="#" class="btn-book" style="border-color:#ff4040;color:#ff4040;">
                        SEE RANKING →
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="pt-5 "
        style="
                background-image: url('{{ asset('images/home_back.png') }}');
                background-size: cover;
                background-position: center top;
                background-repeat: no-repeat;
                width: 100%;
            ">

        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <button class="nav-link active movie-tab" data-bs-toggle="tab" data-bs-target="#nowPlaying">
                    Now Playing
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link movie-tab" data-bs-toggle="tab" data-bs-target="#searchMovie">
                    Searching Movie
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3 showtime-bg">

            {{-- showtime schedule --}}
            <div class="tab-pane fade show active" id="nowPlaying">
                {{-- <div class="tab-pane fade show active" id="nowPlaying">

                    @foreach ($movies as $movie)
                        <div class="movie-row p-4">

                            <div class="row">

                                <!-- movie poster images -->
                                <div class="col-2 text-end mt-2">
                                    <img src="{{ asset('images/' . $movie->image) }}" alt="{{ $movie->title }}"
                                        class="img-showtime">
                                </div>

                                <!-- Right side -->
                                <div class="col-10">

                                    <!-- title -->
                                    <div class="mb-4">
                                        <a href="#" class="movie-title text-decoration-none">
                                            {{ strtoupper($movie->title) }} >
                                        </a>
                                    </div>

                                    <!-- display movies -->
                                    <div class="d-flex gap-3 flex-wrap">
                                        @foreach ($movie->showtimes as $showtime)
                                            <div class="showtime-card">
                                                <div class="showtime-top">
                                                    <div class="showtime-time">
                                                        {{ \Carbon\Carbon::parse($showtime->start_time)->format('H:i') }}
                                                        <div class="showtime-end">
                                                            ～{{ \Carbon\Carbon::parse($showtime->end_time)->format('H:i') }}
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <div class="thater-text">
                                                            Theater
                                                        </div>
                                                        <div class="theater-number theater-box">
                                                            {{ $showtime->theater_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="showtime-bottom">
                                                    @if ($showtime->is_closed)
                                                        <div class="closed-icon">✕</div>
                                                        <div class="closed-text">
                                                            Closed
                                                        </div>
                                                    @else
                                                        <div class="reservation-icon">○</div>
                                                        <div class="reservation-text">
                                                            Reservation
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> --}}
                <div class="movie-row p-4">
                    <div class="row">
                        <!-- image -->
                        <div class="col-2 text-end mt-2">
                            <img src="{{ asset('images/king.png') }}" alt="lionking" class="img-showtime">
                        </div>
                        <!-- 右側 -->
                        <div class="col-10">
                            <!-- title -->
                            <div class="mb-4">
                                <a href="#" class="movie-title text-decoration-none">
                                    LION KING >
                                </a>
                            </div>
                            <!-- schedule -->
                            <div class="showtime-card">

                                <div class="showtime-top">

                                    <div class="showtime-time">
                                        09:55
                                        <div class="showtime-end">〜13:15</div>
                                    </div>

                                    <div class="ms-3">
                                        <div class="thater-text">Theater</div>
                                        <div class="theater-number theater-box">2</div>
                                    </div>

                                </div>

                                <div class="showtime-bottom">

                                    <div class="closed-icon">✕</div>

                                    <div class="closed-text">
                                        Closed
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

     
            {{-- searching movie --}}
            <div class="tab-pane fade" id="searchMovie">
                {{-- SEARCH FORM --}}
                <div class="mt-3">
                    <form action="#" method="GET">

                    <div class="movie-search-wrapper">

                        <div class="movie-search-box">

                            <i class="fa-solid fa-magnifying-glass movie-search-icon"></i>

                            <input type="text" name="keyword" class="movie-search-input" placeholder="Search movies..."
                                value="{{ request('keyword') }}">

                            <button type="submit" class="movie-search-btn">
                                SEARCH
                            </button>

                        </div>

                    </div>

                </form>
                </div>
                

                {{-- POPULAR GENRES --}}
                <div class="movie-search-genres">

                    <p class="movie-search-genres-title">
                        Popular Genres
                    </p>

                    <div class="movie-search-genres-list">

                        <span class="movie-search-chip">Action</span>
                        <span class="movie-search-chip">Adventure</span>
                        <span class="movie-search-chip">Animation</span>
                        <span class="movie-search-chip">Comedy</span>
                        <span class="movie-search-chip">Drama</span>
                        <span class="movie-search-chip">Sci-Fi</span>

                    </div>

                </div>

                {{-- SEARCH RESULTS --}}
                @isset($searchResults)
                    <div class="container mt-5">

                        <h3 class="text-white mb-4">
                            Search Results
                        </h3>

                        <div class="row">

                            @forelse($searchResults as $movie)
                                <div class="col-md-3 mb-4">

                                    <div class="movie-search-card">

                                        <img src="{{ $movie->poster_url }}" class="movie-search-poster">

                                        <div class="movie-search-info">

                                            <div class="movie-search-movie-title">
                                                {{ $movie->title }}
                                            </div>

                                            <div class="movie-search-meta">

                                                <span class="movie-search-rating">
                                                    ⭐ {{ $movie->review_average }}
                                                </span>

                                                <span>
                                                    ⏱ {{ floor($movie->duration / 60) }}h
                                                    {{ $movie->duration % 60 }}m
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <p class="text-white text-center">
                                    No movies found.
                                </p>
                            @endforelse

                        </div>

                    </div>
                @endisset

            </div>

        </div>


    </div>
@endsection
