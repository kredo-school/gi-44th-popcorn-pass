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

    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active">
            </button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1">
            </button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2">
            </button>
        </div>

        <div class="carousel-inner">
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

            {{-- WELCOME --}}
            <div class="carousel-item active">
                <img src="{{ asset('images/welcome.png') }}" class="hero-image-welcome">
                <div class="hero-content-welcome">
                    <p>
                        Experience the Magic of Movies
                    </p>
                    @auth
                        <a href="#" class="btn btn-white">
                            My Page
                        </a>
                    @else
                        <a href="/login" class="btn btn-white">
                            Log in
                        </a>
                    @endauth
                </div>
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
                    <a href="#" class="btn-book btn-book-red">
                        SEE RANKING →
                    </a>
                </div>
            </div>
        </div>

    </div>
    <div class="mt-0 home-hero-bg">
        <div>
            {{-- SEARCH --}}
            <div class="search-wrapper w-50 container pt-5 mb-5">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" class="search-input mt-3" placeholder="Search movies, genres, or showtimes...">

                <button class="search-btn">
                    SEARCH
                </button>
            </div>

            {{-- ===========================
                 Nearby Cinemas
                 =========================== --}}
            <div class="container-fluid section-gap" id="NearbyCinemas">
                <div class="d-flex justify-content-between align-items-center ms-5 me-5">
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


            {{-- Top Ranking --}}
            <div class="">

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
                    @foreach ($order as $rankIndex)
                        @if (isset($topMovies[$rankIndex]))
                            @php
                                $movie = $topMovies[$rankIndex];
                                $s = $sizes[$rankIndex];
                            @endphp

                            <div
                                class="
                    col-md-3
                    {{ $rankIndex == 0 ? 'order-md-2' : '' }}
                    {{ $rankIndex == 1 ? 'order-md-1' : '' }}
                    {{ $rankIndex == 2 ? 'order-md-3' : '' }}
                    ">
                                <div class="ranking-card-wrapper">

                                    <div class="rank-number rank-{{ $rankIndex + 1 }}">
                                        {{ $rankIndex + 1 }}
                                    </div>
                                    @if ($rankIndex == 0)
                                        <div class="top-crown">👑</div>
                                    @endif
                                    <div class="top-card {{ $s['class'] }}">
                                        <div class="poster-area" style="height:{{ $s['height'] }}">
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
                                                    ⭐ {{ $movie->review_average }}
                                                </span>

                                                <span class="meta-divider">|</span>

                                                <span class="movie-duration">
                                                    ⏱ {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m
                                                </span>
                                            </div>
                                            <a
                                                href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}">
                                                <button class=" mt-2 w-100 ranking-book-btn">BOOK NOW</button>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endif
                    @endforeach
                </div>
            </div>

            {{-- Nowplaying --}}
            <div class="container-fluid px-0 section-gap" id="Nowplaying">
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
                        <a href="{{ route('movie.showtime.display') }}" class="view-all fs-5">View All Showtimes</a>
                    </div>

                    <div class="d-flex align-items-center px-3 m-3 gap-2">

                        <!-- left button slider-->
                        <button
                            id="nowPlayingPrevBtn"
                            type="button"
                            class="slider-btn slider-btn-left">
                            <i class="fa-solid fa-circle-chevron-left text-secondary"></i>
                        </button>


                        <!-- Movie Cards scroll -->
                        <div class="flex-grow-1 now-playing-track-wrap">
                            <div class="d-flex gap-3 pb-2 now-playing-track" id="nowPlayingSlider">

                                @foreach ($movies as $movie)
                                    <div class="now-playing-slide">
                                        <div class="movie-card">
                                            <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                <img src="{{ asset($movie->poster_url) }}" class="movie-poster w-100">
                                            </a>
                                            <div class="movie-info" style="background:#081729">

                                                <h6 class="text-white text-center mb-2 mt-2">
                                                    {{ $movie->title }}
                                                </h6>
                                                <div class="movie-meta text-white small px-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-white-50">Adventure</span>
                                                        <span>{{ $movie->duration }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-white-50 genre-label">Genre</span>
                                                        <span>⭐{{ $movie->review_avarage }}</span>
                                                    </div>
                                                </div>
                                                
                                                    href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}">
                                                    <button class="book-btn mt-2 w-100">BOOK NOW</button>
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>

                        <!-- right button slider-->
                        <button
                            id="nowPlayingNextBtn"
                            type="button"
                            class="slider-btn slider-btn-right">
                            <i class="fa-solid fa-circle-chevron-right text-secondary"></i>
                        </button>

                    </div>

                </div>

            </div>

            {{-- Coming sooon --}}
            <div class="container-fluid px-0 section-gap" id="Comingsoon">
                <div class="section-title-wrap">
                    <div class="coming-title-line"></div>
                    <h1 class="coming-title">
                        🎞️ COMING SOON
                    </h1>
                    <div class="coming-title-line"></div>
                </div>

                <div class="panel-navy-overlay">

                    {{-- scroll --}}
                    <div class="position-relative px-3 pt-4">
                        <div class="d-flex gap-3 pb-2 mt-5 ms-4 me-5 coming-soon-track" id="comingSoonSlider">

                            @foreach ($comingSoonMovies as $movie)
                                <a href="{{ route('release', ['movie' => $movie->id]) }}" class="coming-card text-decoration-none flex-shrink-0 m-4"
                                    style="scroll-snap-align: start; width: 400px;">
                                    <div style="overflow: hidden;">
                                        <img src="{{ $movie->poster_url }}" alt="Movie"
                                            style="width: 100%; height: 360px; object-fit: cover; display: block;">
                                        <div class="" style="background: rgba(255,255,255,0.85);">

                                            <div class="coming-movie-info">
                                                <p class="mb-0 text-center coming-movie-title">
                                                    {{ $movie->title }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>

                                        @php
                                            $releaseDate = \Carbon\Carbon::parse($movie->released_date);
                                            $daysLeft = (int) now()->diffInDays($releaseDate, false);
                                        @endphp

                                        <p class="text-white text-center mt-2">

                                            @if ($daysLeft == 0)
                                                <span class="badge bg-danger">🔥 TODAY</span>
                                            @elseif ($daysLeft <= 7)
                                                <span class="badge bg-warning text-dark countdown-badge">
                                                    ⏳ In {{ $daysLeft }} days
                                                </span><br>
                                                <span class="display-4 coming-text fw-bold base">
                                                    {{ $releaseDate->format('j.n.Y') }}
                                                </span>
                                            @elseif ($daysLeft <= 30)
                                                <span class="coming-badge">
                                                    📅 In {{ $daysLeft }} days
                                                </span><br>
                                                <span class="display-4 text-white fw-bold base">
                                                    {{ $releaseDate->format('j.n.Y') }}
                                                </span>
                                            @else
                                                <span class="text-secondary">
                                                    Published on
                                                </span><br>
                                                <span class="display-4 text-white fw-bold base">
                                                    {{ $releaseDate->format('j.n.Y') }}
                                                </span>
                                            @endif

                                            <br>
                                        </p>
                                        <div class="coming-title-line"></div>
                                    </div>


                                </a>
                            @endforeach


                        </div>
                        <div class="">
                            {{-- right slider button --}}
                            <button
                                id="comingSoonNextBtn"
                                type="button"
                                class="coming-slider-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>


                    </div>
                </div>
            </div>

            <hr class="text-white">

            {{-- food & Drink --}}
            <div class="container-fluid px-0 mt-5 section-gap" id="Food&drink">
                <p class="display-3 text-white title-base ms-5">
                    🍿 Food & Drink
                </p>

                <div class="w-75 mx-auto panel-navy-overlay">
                    <img src="{{ asset('images/foodmenu.png') }}" alt="foodmenu" class="w-100 food-menu-img">
                </div>

            </div>

            <hr class="text-white">

            {{-- Information ここなおす--}}
            <div class="container-fluid px-0 mt-5 section-gap" id="Information">
                <p class="display-3 text-white title-base ms-5 text-center">
                    Information
                </p>
                <div class="container">
                    <div class="row g-3">git add resources/views/home.blade.php
                        @for ($i = 0; $i < 8; $i++)
                            <div class="col-3 ">
                                <div class="card rounded-0 news-card">
                                    <div class="card-head text-center">
                                        <div class="w-50 bg-warning mx-auto">
                                            NEWS
                                        </div>
                                    </div>
                                    <img src="{{ asset('images/news.png') }}" class="card-img-top news-img">
                                    <div class="card-body">
                                        If you're a member, you get great deals every Friday!
                                    </div>
                                </div>
                            </div>
                        @endfor
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

    </div>
    <script src="{{ asset('js/home.js') }}" defer></script>
@endsection