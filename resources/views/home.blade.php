@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    {{-- ===========================
         Location Permission Dialog
         =========================== --}}
    <div id="locationPermissionOverlay" class="location-overlay">
        <div class="location-dialog">
            <div class="location-dialog-icon">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <h5 class="mb-2">Would you like to share your location?</h5>
            <p class="small text-white-50 mb-4">Your location will be used to show nearby cinemas.</p>
            <div class="d-flex flex-column gap-2">
                <button type="button" class="btn location-btn location-btn-primary" data-choice="always">
                    Always Allow
                </button>
                <button type="button" class="btn location-btn location-btn-secondary" data-choice="once">
                    Allow Once
                </button>
                <button type="button" class="btn location-btn location-btn-outline" data-choice="deny">
                    Don't Allow
                </button>
            </div>
        </div>
    </div>

    <div class="swiper heroSwiper">

        <div class="swiper-wrapper">

            {{-- WELCOME --}}
            <div class="swiper-slide">
                <img src="{{ asset('images/layouts/welcome.png') }}" class="hero-image-welcome">

                <div class="hero-content-welcome">
                    <p>
                        Experience the Magic of Movies
                    </p>

                    @auth
                        <a href="{{ route('mypage.dashboard') }}" class="btn btn-book">
                            My Page
                        </a>
                    @else
                        <a href="/login" class="btn btn-book">
                            Log in
                        </a>
                    @endauth
                </div>
            </div>

            {{-- COMING SOON --}}
            <div class="swiper-slide">
                <img src="{{ $heroMovie->banner_image_url }}" class="hero-image">

                <div class="hero-overlay"></div>

                <div class="hero-content">
                    <span class="hero-tag">
                        COMING SOON
                    </span>

                    <h1>
                        UPCOMING<br>
                        BLOCKBUSTERS
                    </h1>

                    <a href="{{ route('release', $heroMovie->id) }}" class="btn-book">
                        VIEW MORE →
                    </a>
                </div>

            </div>

            {{-- TOP RANKING --}}
            <div class="swiper-slide">

                <img src="{{ $topMovie->banner_image_url }}" class="hero-image">

                <div class="hero-overlay"></div>

                <div class="hero-content">

                    <span class="hero-tag hero-tag-red">
                        TOP RANKING
                    </span>

                    <h1>
                        #1 MOVIE<br>
                        OF THE WEEK
                    </h1>

                    <p>
                        Most watched by our audience.
                    </p>

                    <a href="{{ route('movie_detail', $topMovie->id) }}" class="btn-book btn-book-red">

                        SEE MOVIE DETAIL →

                    </a>

                </div>

            </div>

        </div>

        <!-- 矢印 -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- 下の丸 -->
        <div class="swiper-pagination"></div>

    </div>

    <div class="mt-0 home-hero-bg">

        <div class="">




            {{-- Nearby Cinemas  --}}
            <div class="container-fluid" id="NearbyCinemas">
                <div class="d-flex justify-content-between align-items-center ms-5 me-5 pt-5">
                    <p class="display-5 text-white title-base mb-0">
                        📍 Nearby Movie Theaters
                    </p>
                    <button type="button" id="changeLocationPrefBtn" class="btn btn-sm location-change-btn">
                        <i class="fa-solid fa-gear me-1"></i> Location Settings
                    </button>
                </div>

                <div class="panel-navy-overlay">
                    <div id="nearbyCinemasStatus" class="text-white-50 text-center py-4">
                        <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading nearby theaters...
                    </div>

                    <div id="nearbyCinemasList" class="row g-3 p-4 is-hidden"></div>
                </div>
            </div>

            {{-- SEARCH --}}
            <div class="search-wrapper w-100 container ">

                <form action="{{ route('movies.search') }}" method="GET" class="search-wrapper w-50 container pt-5 mb-5">

                    <i class="fa-solid fa-magnifying-glass search-icon"></i>

                    <input type="text" name="keyword" class="search-input mt-3" placeholder="Search by movie title..."
                        value="{{ request('keyword') }}">

                    <button class="search-btn">
                        SEARCH
                    </button>
                </form>
            </div>



            @php
                $order = [1, 0, 2];

                $sizes = [
                    0 => [
                        'height' => '400px',
                        'number' => '20rem',
                        'class' => 'first',
                    ],
                    1 => [
                        'height' => '300px',
                        'number' => '12rem',
                        'class' => 'second',
                    ],
                    2 => [
                        'height' => '300px',
                        'number' => '12rem',
                        'class' => 'third',
                    ],
                ];
            @endphp

            <div class="row justify-content-center align-items-end gx-5">

                {{-- Nowplaying --}}
                <div class="container-fluid px-0 " id="Nowplaying">
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <div class="section-title-wrap">
                                <div class="section-title-line"></div>
                                <h2 class="section-title">
                                    <span class="title-icon">🎬</span>
                                    NOW PLAYING
                                </h2>
                                <div class="section-title-line"></div>
                            </div>

                            <div class="w-100 py-4 panel-navy-overlay">

                                <!-- View All -->
                                <div class="d-flex justify-content-end px-4 mb-3">
                                    <a href="{{ route('movie.showtime.display') }}" class="view-all fs-5">View All
                                        Showtimes</a>
                                </div>

                                <div class="d-flex align-items-center px-3 m-3 gap-2 position-relative">

                                    <!-- left button slider-->

                                    <button id="nowPlayingNextBtn" type="button" class="nowplaying-btn">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>

                                    <!-- Movie Cards scroll -->
                                    <div class="flex-grow-1 now-playing-track-wrap">
                                        <div class="d-flex gap-3 pb-2 now-playing-track" id="nowPlayingSlider">

                                            @foreach ($movies as $movie)
                                                <div class="now-playing-slide">
                                                    <div class="movie-card">
                                                        <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                            <img src="{{ asset($movie->poster_url) }}"
                                                                class="movie-poster w-100">
                                                        </a>
                                                        <div class="movie-info" style="background:#081729">

                                                            <h6 class="text-white text-center mb-2 mt-2">
                                                                {{ $movie->title }}
                                                            </h6>
                                                            <div class="movie-meta text-white small px-2">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span class="text-white-50">Adventure</span>
                                                                    <span>{{ $movie->duration }}</span>
                                                                </div>
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span class="text-white-50 genre-label">Genre</span>
                                                                    <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                                                                        class="text-decoration-none text-white">
                                                                        ⭐ {{ number_format($movie->review_average, 1) }}
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}"
                                                                class="text-decoration-none">
                                                                <button class="book-btn mt-2 w-100">BOOK
                                                                    NOW</button>
                                                            </a>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>

                                    <!-- right button slider-->
                                    <button id="nowPlayingPrevBtn" type="button" class="nowplaying-btn">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                        <div class="col-1"></div>
                    </div>


                </div>


                <div class="row mt-5">
                    <div class="col-1"></div>

                    {{-- Top Ranking --}}
                    <div class="col-12 col-lg-7">

                        <div class="ranking-header">
                            <hr class="ranking-line w-50 mx-auto">
                            <div class="ranking-main-title">
                                👑 TOP 3 MOVIES
                            </div>
                            <div class="ranking-sub-title">
                                WEEKLY RANKING
                            </div>
                            <hr class="ranking-line w-50 mx-auto">
                        </div>
                        @php
                            $order = [1, 0, 2];

                            $sizes = [
                                0 => [
                                    'number' => '20rem',
                                    'class' => 'first',
                                ],
                                1 => [
                                    'number' => '12rem',
                                    'class' => 'second',
                                ],
                                2 => [
                                    'number' => '12rem',
                                    'class' => 'third',
                                ],
                            ];
                        @endphp

                        <div class="row justify-content-center align-items-end gx-3 gx-lg-5">
                            @foreach ($order as $rankIndex)
                                @if (isset($topMovies[$rankIndex]))
                                    @php
                                        $movie = $topMovies[$rankIndex];
                                        $s = $sizes[$rankIndex];
                                    @endphp

                                    <div class="col-4">
                                        <div class="ranking-card-wrapper">

                                            <div class="rank-number rank-{{ $rankIndex + 1 }}">
                                                {{ $rankIndex + 1 }}
                                            </div>

                                            <div class="top-card {{ $s['class'] }}">
                                                <div class="poster-area">
                                                    <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                                            class="w-100 h-100">
                                                    </a>

                                                    <div class="movie-overlay"></div>
                                                </div>

                                                <div class="movie-info-box">
                                                    <div class="movie-title">
                                                        {{ $movie->title }}
                                                    </div>

                                                    <div class="movie-meta">
                                                        <span class="movie-rating">
                                                            <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                                                                class="text-decoration-none text-white">
                                                                ⭐ {{ number_format($movie->review_average, 1) }}
                                                            </a>
                                                        </span>

                                                        <span class="meta-divider">|</span>

                                                        <span class="movie-duration">
                                                            ⏱
                                                            {{ floor($movie->duration / 60) }}h
                                                            {{ $movie->duration % 60 }}m
                                                        </span>
                                                    </div>

                                                    <a
                                                        href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}">
                                                        <button class="mt-2 w-100 ranking-book-btn">
                                                            BOOK NOW
                                                        </button>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>


                    <div class="col-3">
                        <div class="side-ranking ranking-side-background mt-5">


                            @foreach ($topMovies->slice(3, 7)->values() as $index => $movie)
                                <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}" class="ranking-list-item">

                                    <span class="ranking-position">
                                        {{ $index + 4 }}
                                    </span>

                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                        class="ranking-thumb">

                                    <span class="ranking-title">
                                        {{ $movie->title }}
                                    </span>

                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-1"></div>


                </div>

                {{-- Coming soon --}}
                <div class="row" id="Comingsoon">
                    <div class="col-1"></div>
                    <div class="col-10">

                        <div class="container-fluid px-0 section-gap" id="Comingsoon">


                            <div class="panel-navy-overlay">
                                <div class="marquee-header">
                                    <span class="marquee-header-line"></span>
                                    <h1 class="marquee-title">Coming Soon</h1>
                                    <span class="marquee-header-line"></span>
                                </div>
                                <div class="position-relative px-3 pt-3">
                                    <div class="d-flex gap-4 pb-2 mt-5 ms-4 me-5 coming-soon-track" id="comingSoonSlider">

                                        @foreach ($comingSoonMovies as $movie)
                                            @php
                                                $releaseDate = \Carbon\Carbon::parse($movie->released_date);
                                                $daysLeft = (int) now()->diffInDays($releaseDate, false);
                                            @endphp

                                            <a href="{{ route('release', ['movie' => $movie->id]) }}"
                                                class="coming-card text-decoration-none flex-shrink-0"
                                                style="scroll-snap-align: start;">

                                                <div class="coming-poster-wrap">
                                                    <div class="film-sprockets"></div>
                                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                                        class="coming-poster">

                                                    <div
                                                        class="ticket-stub {{ $daysLeft <= 0 ? 'ticket-stub-today' : '' }}">
                                                        <span class="ticket-stub-label">
                                                            @if ($daysLeft <= 0)
                                                                Now showing
                                                            @elseif ($daysLeft == 1)
                                                                Tomorrow
                                                            @else
                                                                {{ $daysLeft }} days
                                                            @endif
                                                        </span>
                                                        <span
                                                            class="ticket-stub-date">{{ $releaseDate->format('M j') }}</span>
                                                    </div>
                                                </div>

                                                <p class="coming-movie-title text-center">
                                                    {{ $movie->title }}</p>
                                            </a>
                                        @endforeach

                                    </div>

                                    <button id="comingSoonNextBtn" type="button" class="coming-slider-btn ">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-1"></div>


                </div>

                <hr class="text-white">

                {{-- Information --}}
                <div class="container-fluid px-0 mt-5 section-gap" id="Information">
                    <p class="display-3 text-white title-base ms-5 text-center">
                        Information
                    </p>
                    <div class="container">
                        <div class="row g-3">
                            @forelse($information as $info)
                                <div class="col-md-3">
                                    <a href="{{ route('information.detail', $info->id) }}" class="text-decoration-none">
                                        <div class="card rounded-0 news-card h-100">

                                            <div class="card-head text-center py-1">
                                                <span class="badge px-3 py-1"
                                                    style="background-color: {{ $info->category->color }}">
                                                    {{ $info->category->name }}
                                                </span>
                                            </div>

                                            @if ($info->image_url)
                                                <img src="{{ $info->image_url }}" alt="{{ $info->title }}"
                                                    class="card-img-top info-list-img">
                                            @else
                                                <div
                                                    class="card-img-top info-list-img d-flex align-items-center justify-content-center bg-white text-dark fw-bold">
                                                    INFORMATION
                                                </div>
                                            @endif

                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-bold">{{ $info->title }}</h6>
                                                <p class="small mb-0">
                                                    {{ Str::limit($info->content, 80) }}</p>
                                            </div>

                                        </div>
                                    </a>
                                </div>
                            @empty
                                <p class="text-white text-center">No information available.</p>
                            @endforelse
                        </div>

                        {{-- View All Button --}}
                        <div class="text-center mt-4">
                            <a href="{{ route('information.index') }}" class="btn btn-outline-warning px-5 py-2">
                                View All Information
                            </a>
                        </div>

                    </div>
                </div>

                <div class="back-to-top mb-5">
                    <a href="#top" class="back-to-top-link">
                        <div class="arrow">
                            <div class="arrow">
                                <i class="fa-solid fa-chevron-up"></i>
                            </div>
                        </div>
                        <span>to top page</span>
                    </a>
                </div>
            </div>
        </div>



        <script src="{{ asset('js/home.js') }}" defer></script>

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @endsection
