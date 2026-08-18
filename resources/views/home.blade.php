@extends('layouts.app')

@section('content')
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

    <div class="home-hero">

        {{-- SEARCH --}}
        <div class="search-wrapper">
            <form action="{{ route('movies.search') }}" method="GET">

                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" name="keyword" class="search-input" placeholder="Search by movie title..."
                    value="{{ request('keyword') }}">

                <button class="search-btn">
                    SEARCH
                </button>
            </form>
        </div>

        {{-- Contact icon --}}
        <div class="contact-icon">

            <a href="{{ route('customer.chat.index') }}">
                <i class="fa-solid fa-comment"></i>
            </a>

            @if (isset($unreadMessages) && $unreadMessages > 0)
                <span class="chat-notification">
                    {{ $unreadMessages }}
                </span>
            @endif
        </div>

        {{-- Swiper section --}}
        <div class="mx-auto">
            <div class="swiper-position">
                <div class="swiper heroSwiper swiper-bg">
                    <div class="swiper-wrapper">

                        {{-- Slide 1: Welcome --}}
                        <div class="swiper-slide">
                            <div class="w-75 mx-auto row justify-content-center h-100">
                                <div class="position-relative h-100 col-10">
                                    <img src="{{ asset('images/layouts/welcome.png') }}" class="hero-image-welcome">
                                </div>
                            </div>
                        </div>

                        {{-- Slide 2: Coming Soon --}}
                        @if (isset($heroMovie) && $heroMovie)
                            <div class="swiper-slide">
                                <div class="w-75 mx-auto h-100">
                                    <div class="row g-0 align-items-center justify-content-center h-100">

                                        <div class="col-lg-4 position-relative h-75 hero-left">
                                            <img src="{{ $heroMovie->banner_image_url }}" class="hero-image"
                                                alt="{{ $heroMovie->title }}">

                                            <div class="hero-overlay"></div>

                                            <div class="hero-content">
                                                <span class="hero-tag">COMING SOON</span>

                                                <h1>
                                                    UPCOMING<br>
                                                    BLOCKBUSTERS
                                                </h1>

                                                <a href="{{ route('release', $heroMovie->id) }}"
                                                    class="btn-book btn-book-yellow">
                                                    VIEW MORE →
                                                </a>
                                            </div>
                                        </div>

                                        <div
                                            class="col-lg-4 d-flex align-items-center justify-content-center h-100 ps-lg-5">
                                            <iframe class="hero-video" src="{{ $heroMovie->trailer_url }}"
                                                title="{{ $heroMovie->title }} Trailer"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allow="autoplay; encrypted-media" allowfullscreen>
                                            </iframe>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif


                        {{-- Slide 3: Top Ranking --}}
                        @if (isset($topMovie) && $topMovie)
                            <div class="swiper-slide">
                                <div class="w-75 mx-auto h-100">
                                    <div class="row g-0 align-items-center justify-content-center h-100">

                                        <div class="col-lg-4 position-relative h-75 hero-left">
                                            <img src="{{ $topMovie->banner_image_url }}" class="hero-image"
                                                alt="{{ $topMovie->title }}">

                                            <div class="hero-overlay"></div>

                                            <div class="hero-content">
                                                <span class="hero-tag hero-tag-red">
                                                    TOP RANKING
                                                </span>

                                                <h1>
                                                    #1 MOVIE<br>
                                                    OF THE WEEK
                                                </h1>

                                                <p>Most watched by our audience.</p>

                                                <a href="{{ route('movie_detail', $topMovie->id) }}"
                                                    class="btn-book btn-book-red">
                                                    SEE MOVIE DETAIL →
                                                </a>
                                            </div>
                                        </div>

                                        <div
                                            class="col-lg-4 d-flex align-items-center justify-content-center h-100 ps-lg-5">
                                            <iframe class="hero-video" src="{{ $topMovie->trailer_url }}"
                                                title="{{ $topMovie->title }} Trailer"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen>
                                            </iframe>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Slide 4: Information --}}
                        <div class="swiper-slide">
                            <div class="w-75 mx-auto h-100">
                                <div class="row g-0 align-items-center justify-content-center">

                                    <div class="col-lg-10 d-flex align-items-center justify-content-center h-100 ps-lg-5">
                                        <div class="info-card">
                                            <span class="hero-tag hero-tag-blue">INFORMATION</span>
                                            @if (isset($information_slide) && $information_slide)
                                                <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                                                    <span class="hero-tag mb-0"
                                                        style="background-color: {{ optional($information_slide->category)->color ?? '#6c757d' }};
                                                                         color: {{ optional($information_slide->category)->text_color ?? '#fff' }};">
                                                        {{ optional($information_slide->category)->name ?? 'General' }}
                                                    </span>
                                                    <h2 class="info-card-title mb-0">
                                                        {{ $information_slide->title }}
                                                    </h2>

                                                    <div class="info-card-date">
                                                        {{ $information_slide->published_at?->format('Y.m.d') }}
                                                    </div>
                                                </div>

                                                <a href="{{ route('information.detail', $information_slide->id) }}"
                                                    class="btn-book-black">
                                                    VIEW MORE →
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- Navigation --}}
                    <div class="swiper-button-prev">
                        <i class="fa-solid fa-chevron-left"></i>
                    </div>

                    <div class="swiper-button-next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>

                    {{-- Pagination --}}
                    <div class="swiper-pagination"></div>

                </div>
            </div>
        </div>

        {{-- ===========================
     Nearby Cinemas
     =========================== --}}
        <div class="row mt-5" id="NearbyCinemas">
            <div class="col-1"></div>

            <div class="col-10">

                {{-- Section title --}}
                <div class="section-title-wrap d-flex justify-content-between align-items-center">
                    <h2 class="section-title mb-0">
                        <span class="title-icon">📍</span>
                        NEARBY MOVIE THEATERS
                    </h2>

                    <button type="button" id="changeLocationPrefBtn" class="btn btn-sm location-change-btn">
                        <i class="fa-solid fa-gear me-1"></i>
                        Location Settings
                    </button>
                </div>

                {{-- Nearby cinemas panel --}}
                <div class="panel-navy-overlay py-4 px-3">

                    {{-- Mini Map --}}
                    <div class="nearby-map-wrapper mb-4">

                        <div id="nearbyCinemaMap" class="nearby-cinema-map" data-full-map-url="{{ url('/map') }}">
                        </div>

                        {{-- Map footer --}}
                        <div class="nearby-map-footer">

                            {{-- Map legend --}}
                            <div class="nearby-map-legend">

                                <span class="nearby-map-legend-item">
                                    <span class="legend-dot legend-dot-current"></span>
                                    Current Cinema
                                </span>

                                <span class="nearby-map-legend-item">
                                    <span class="legend-dot legend-dot-location"></span>
                                    Your Location
                                </span>

                            </div>

                            {{-- Full map button --}}
                            <a href="{{ url('/map') }}" class="btn btn-sm nearby-full-map-btn">
                                <i class="fa-solid fa-map-location-dot me-1"></i>
                                Open Full Map
                                <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>

                        </div>
                    </div>

                    {{-- Loading / status --}}
                    <div id="nearbyCinemasStatus" class="text-white-50 text-center py-4">
                        <i class="fa-solid fa-spinner fa-spin me-2"></i>
                        Loading nearby theaters...
                    </div>

                    {{-- Cinema cards --}}
                    <div id="nearbyCinemasList" class="row g-3 is-hidden"></div>

                </div>
            </div>

            <div class="col-1"></div>
        </div>

        <hr class="text-white">

        {{-- Top Ranking --}}
        <div class="row mt-5">
            <div class="col-1"></div>

            <div class="col-10">
                <div class="section-title-wrap">
                    <h2 class="section-title">
                        <span class="title-icon">👑</span>
                        TOP RANKING
                    </h2>
                </div>

                <div class="panel-navy-overlay py-4 px-3">
                    <div class="row">

                        {{-- Top 3 --}}
                        <div class="col-lg-8">
                            <div class="ranking-header">
                                <div class="ranking-sub-title">
                                    WEEKLY RANKING
                                </div>

                                <div class="ranking-main-title">
                                    <h1>TOP 3 MOVIES</h1>
                                </div>
                            </div>

                            @php
                                $order = [1, 0, 2];

                                $sizes = [
                                    0 => ['class' => 'first'],
                                    1 => ['class' => 'second'],
                                    2 => ['class' => 'third'],
                                ];
                            @endphp

                            <div class="row justify-content-center align-items-end gx-3">
                                @if (isset($topMovies))
                                    @foreach ($order as $rankIndex)
                                        @if (isset($topMovies[$rankIndex]))
                                            @php
                                                $movie = $topMovies[$rankIndex];
                                                $size = $sizes[$rankIndex];
                                            @endphp

                                            <div class="col-4">
                                                <div class="ranking-card-wrapper">
                                                    <div class="rank-number rank-{{ $rankIndex + 1 }}">
                                                        {{ $rankIndex + 1 }}
                                                    </div>

                                                    <div class="top-card {{ $size['class'] }}">
                                                        <div class="poster-area">
                                                            <a
                                                                href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                                <img src="{{ asset($movie->poster_url) }}"
                                                                    alt="{{ $movie->title }}" class="w-100 h-100">
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

                                                                <span class="text-white">|</span>

                                                                <span class="movie-duration">
                                                                    ⏱ {{ floor($movie->duration / 60) }}h
                                                                    {{ $movie->duration % 60 }}m
                                                                </span>
                                                            </div>

                                                            <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}"
                                                                class="text-decoration-none">
                                                                <span
                                                                    class="ranking-book-btn mt-2 w-100 d-block text-center">
                                                                    BOOK NOW
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        {{-- Ranking 4–10 --}}
                        <div class="col-lg-4">
                            <div class="side-ranking ranking-side-background mt-1">
                                @if (isset($topMovies))
                                    @foreach ($topMovies->slice(3, 7)->values() as $index => $movie)
                                        <div class="ranking-list-item">

                                            <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}"
                                                class="ranking-movie-link text-decoration-none">

                                                <span class="ranking-position">
                                                    {{ $index + 4 }}
                                                </span>

                                                <img src="{{ asset($movie->poster_url) }}" class="ranking-thumb"
                                                    alt="{{ $movie->title }}">

                                                <span class="ranking-title">
                                                    {{ $movie->title }}
                                                </span>

                                            </a>

                                            <div class="ranking-book-area">
                                                <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}"
                                                    class="ranking-slide-book-btn text-decoration-none">
                                                    BOOK NOW
                                                </a>
                                            </div>

                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-1"></div>
        </div>

        <hr class="text-white">



        {{-- Recommended for You --}}
        @auth
            <div class="row mt-5 home-recommendations" id="Recommendations">
                <div class="col-1"></div>

                <div class="col-10">
                    <div class="section-title-wrap">
                        <h2 class="section-title">
                            <span class="title-icon">🎯</span>
                            RECOMMENDED FOR YOU
                        </h2>
                    </div>

                    <div class="panel-navy-overlay py-4 px-4">
                        <div id="recommendations-container" class="row g-3 justify-content-center">
                            <div class="col-12 text-center text-white">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">
                                        Loading...
                                    </span>
                                </div>

                                <p class="text-white-50 mt-2 mb-0">
                                    Loading recommendations...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-1"></div>
            </div>

            <hr class="text-white">
        @endauth

        {{-- Now Showing --}}
        <div class="container-fluid px-0" id="Nowshowing">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <div class="section-title-wrap">
                        <h2 class="section-title">
                            <span class="title-icon">🎬</span>
                            NOW SHOWING
                        </h2>
                    </div>

                    <div class="w-100 py-4 panel-navy-overlay">

                        <!-- View All -->
                        <div class="d-flex justify-content-end px-4 mb-3">
                            <a href="{{ route('movie.showtime.display') }}" class="view-all fs-5">
                                View All Showtimes
                            </a>
                        </div>

                        <div class="d-flex align-items-center px-3 m-3 gap-2 position-relative">

                            <!-- Prev button slider -->
                            <button id="nowShowingPrevBtn" type="button" class="nowshowing-btn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>

                            <!-- Movie Cards scroll -->
                            <div class="flex-grow-1 now-showing-track-wrap">
                                <div class="d-flex gap-3 pb-2 now-showing-track" id="nowShowingSlider">

                                    @if (isset($movies))
                                        @foreach ($movies as $movie)
                                            <div class="now-showing-slide">
                                                <div class="movie-card">
                                                    <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                        <img src="{{ asset($movie->poster_url) }}"
                                                            class="movie-poster w-100">
                                                    </a>
                                                    <div class="movie-info" style="background:#081729">

                                                        <h6 class="text-white text-center mb-2 mt-2">
                                                            {{ $movie->title }}
                                                        </h6>
                                                        <div class="movie-meta">
                                                            <span class="movie-rating">
                                                                <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                                                                    class="text-decoration-none text-white">
                                                                    ⭐ {{ number_format($movie->review_average, 1) }}
                                                                </a>
                                                            </span>

                                                            <span class="text-white">|</span>

                                                            <span class="movie-duration">
                                                                ⏱ {{ floor($movie->duration / 60) }}h
                                                                {{ $movie->duration % 60 }}m
                                                            </span>
                                                        </div>

                                                        <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}"
                                                            class="text-decoration-none">
                                                            <button class="book-btn mt-2 w-100">
                                                                BOOK NOW
                                                            </button>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>

                            <!-- Next button slider -->
                            <button id="nowShowingNextBtn" type="button" class="nowshowing-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>

                        </div>

                    </div>
                </div>
                <div class="col-1"></div>
            </div>

            <hr class="text-white">

            {{-- Coming soon --}}
            <div class="row" id="Comingsoon">
                <div class="col-1"></div>
                <div class="col-10">
                    <div class="section-title-wrap">
                        <h2 class="section-title">
                            <span class="title-icon">🎞️</span>
                            COMING SOON
                        </h2>
                    </div>

                    <div class="container-fluid px-0 section-gap">
                        <div class="panel-navy-overlay">
                            <div class="position-relative px-3 pt-3">
                                <div class="d-flex gap-4 pb-2 mt-5 ms-4 me-5 coming-soon-track" id="comingSoonSlider">

                                    @if (isset($comingSoonMovies))
                                        @foreach ($comingSoonMovies as $movie)
                                            @php
                                                $releaseDate = \Carbon\Carbon::parse(
                                                    $movie->released_date,
                                                )->startOfDay();
                                                $today = now()->startOfDay();
                                                $daysLeft = $today->diffInDays($releaseDate, false);
                                            @endphp

                                            <a href="{{ route('release', ['movie' => $movie->id]) }}"
                                                class="coming-card text-decoration-none flex-shrink-0"
                                                style="scroll-snap-align: start;">

                                                <div class="coming-poster-wrap mb-3">
                                                    <div class="film-sprockets"></div>

                                                    <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}"
                                                        class="coming-poster">

                                                    <div
                                                        class="ticket-stub {{ $daysLeft <= 0 ? 'ticket-stub-today' : '' }}">
                                                        <span class="ticket-stub-label">
                                                            @if ($daysLeft <= 0)
                                                                Today
                                                            @elseif ($daysLeft == 1)
                                                                Tomorrow
                                                            @else
                                                                {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }}
                                                            @endif
                                                        </span>

                                                        <span class="ticket-stub-date">
                                                            {{ $releaseDate->format('M j') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Prev button -->
                                <button id="comingSoonPrevBtn" type="button" class="comingsoon-btn">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>

                                <!-- Next button -->
                                <button id="comingSoonNextBtn" type="button" class="comingsoon-btn">
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
            <div class="row" id="Information">
                <div class="col-1"></div>
                <div class="col-10">
                    <div class="section-title-wrap">
                        <h2 class="section-title">
                            <span class="title-icon">📢</span>
                            INFORMATION
                        </h2>
                    </div>

                    <div class="container-fluid px-0 section-gap">
                        <div class="info-home-list">

                            @if (isset($information))
                                @forelse($information as $info)
                                    <a href="{{ route('information.detail', $info->id) }}" class="text-decoration-none">

                                        <div class="info-list-item">

                                            {{-- Category --}}
                                            <span class="info-list-badge"
                                                style="background-color: {{ optional($info->category)->color ?? '#6c757d' }};
                                                        color: {{ optional($info->category)->text_color ?? '#fff' }};">
                                                {{ optional($info->category)->name ?? 'General' }}
                                            </span>

                                            {{-- Title --}}
                                            <span class="info-list-title">
                                                {{ $info->title }}
                                            </span>

                                            {{-- Published Date --}}
                                            <span class="info-list-date">
                                                {{ $info->published_at?->format('Y.m.d') }}
                                            </span>

                                            {{-- Arrow --}}
                                            <span class="info-list-arrow">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </span>

                                        </div>

                                    </a>
                                @empty
                                    <p class="text-white text-center">No information available.</p>
                                @endforelse
                            @endif

                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('information.index') }}" class="btn btn-outline-warning px-5 py-2">
                                View All Information
                            </a>
                        </div>

                    </div>
                </div>
                <div class="col-1"></div>
            </div>

            <div class="back-to-top">
                <a href="#top" class="back-to-top-link">
                    <div class="arrow">
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <span>to top page</span>
                </a>
            </div>
        </div>
    </div>
@endsection

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('maps.google.api_key') }}" defer></script>
