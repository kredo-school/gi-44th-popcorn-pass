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
                    <span class="hero-tag" style="color:#ff4040;">
                        TOP RANKING
                    </span>
                    <h1>
                        #1 MOVIE<br>
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
    <div class="mt-0"
        style="
                background-image: url('{{ asset('images/home_back.png') }}');
                background-size: cover;
                background-position: center top;
                background-repeat: no-repeat;
                width: 100%;
            ">
        <div>
            {{-- SEARCH --}}
            <div class="search-wrapper w-50 container pt-5 mb-5">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" class="search-input mt-3" placeholder="Search movies, genres, or showtimes...">

                <button class="search-btn">
                    SEARCH
                </button>
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

                                    <div class="rank-number rank-{{ $rankIndex + 1 }}"
                                        style="
                            font-size: {{ $s['number'] }};
                            ">
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

                <div class="w-100 py-4" style="background: rgba(16, 57, 133, 0.5);">

                    <!-- View All -->
                    <div class="d-flex justify-content-end px-4 mb-3">
                        <a href="{{ route('movie.showtime.display') }}" class="view-all fs-5">View All Showtimes</a>
                    </div>

                    <div class="d-flex align-items-center px-3 m-3 gap-2">

                        <!-- left button slider-->
                        <button
                            onclick="document.getElementById('nowPlayingSlider').scrollBy({left: -280, behavior: 'smooth'})"
                            class="border-0 bg-transparent text-white flex-shrink-0"
                            style="font-size: 2.5rem; width: 60px; min-width: 60px;">
                            <i class="fa-solid fa-circle-chevron-left text-secondary"></i>
                        </button>


                        <!-- Movie Cards scroll -->
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex gap-3 pb-2" id="nowPlayingSlider"
                                style="overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none;">

                                @foreach ($movies as $movie)
                                    <div class="flex-shrink-0" style="scroll-snap-align: start; width: 200px;">
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
                                                        <span class="text-white-50"
                                                            style="font-size:0.75rem;">Genre</span>
                                                        <span>⭐{{ $movie->review_avarage }}</span>
                                                    </div>
                                                </div>
                                                <a
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
                            onclick="document.getElementById('nowPlayingSlider').scrollBy({left: 280, behavior: 'smooth'})"
                            class="border-0 bg-transparent text-white flex-shrink-0"
                            style="font-size: 2.5rem; width: 40px;">
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

                <div style="background: rgba(16, 57, 133, 0.5)">

                    {{-- scroll --}}
                    <div class="position-relative px-3 pt-4">
                        <div class="d-flex gap-3 pb-2 mt-5 ms-4 me-5" id="comingSoonSlider"
                            style="overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none;">

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
                                                <span class="badge bg-danger">🎉 TODAY</span>
                                            @elseif ($daysLeft <= 7)
                                                <span class="badge bg-warning text-dark countdown-badge">
                                                    🔥 In {{ $daysLeft }} days
                                                </span><br>
                                                <span class="display-4 coming-text fw-bold base">
                                                    {{ $releaseDate->format('j.n.Y') }}
                                                </span>
                                            @elseif ($daysLeft <= 30)
                                                <span class="coming-badge">
                                                    ⏳ In {{ $daysLeft }} days
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
                                onclick="document.getElementById('comingSoonSlider').scrollBy({left: 280, behavior: 'smooth'})"
                                class="position-absolute top-0 end-0 border-0 text-white d-flex align-items-center justify-content-center mt-5 me-2"
                                style="
                            height: 85%;
                            width: 45px;
                            background: rgba(50, 50, 70, 0.7);
                            font-size: 2.5rem;
                            z-index: 10;
                            backdrop-filter: blur(2px);
                            border-radius: 8px;">
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

                <div class="w-75 mx-auto" style="background: rgba(16, 57, 133, 0.5);">
                    <img src="{{ asset('images/foodmenu.png') }}" alt="foodmenu" class="w-100"
                        style="display: block; object-fit: cover;">
                </div>

            </div>

            <hr class="text-white">

            {{-- Information --}}
            <div class="container-fluid px-0 mt-5 section-gap" id="Information">
                <p class="display-3 text-white title-base ms-5 text-center">
                    Information
                </p>
                <div class="container">
                    <div class="row g-3">
                        @for ($i = 0; $i < 8; $i++)
                            <div class="col-3 ">
                                <div class="card rounded-0" style="background: #D9D9D9">
                                    <div class="card-head text-center">
                                        <div class="w-50 bg-warning mx-auto">
                                            NEWS
                                        </div>
                                    </div>
                                    <img src="{{ asset('images/news.png') }}" class="card-img-top"
                                        style="object-fit: cover; height: 200px;">
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
@endsection
