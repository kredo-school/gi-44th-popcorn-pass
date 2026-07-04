@extends('layouts.app')

@section('content')

    {{-- ===========================
         Location Permission Dialog
         =========================== --}}
    <div id="locationPermissionOverlay" class="location-overlay" style="display:none;">
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

                <div style="background: rgba(16, 57, 133, 0.5)">
                    <div id="nearbyCinemasStatus" class="text-white-50 text-center py-4">
                        <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading nearby theaters...
                    </div>

                    <div id="nearbyCinemasList" class="row g-3 p-4" style="display:none;"></div>
                </div>
            </div>


            {{-- Top Ranking --}}
            <div class="container-fuild section-gap" id="Topranking">
                <p class="display-3 text-white title-base ms-5 ">
                    👑 Top Ranking
                </p>

                {{-- Top 3 --}}
                <div style="background: rgba(16, 57, 133, 0.5)">
                    <div class="row justify-content-center align-items-end g-5 mt-2 pb-5">

                        @php
                            $sizes = [
                                0 => [
                                    'top' => '-6vw',
                                    'left' => '-1vw',
                                    'font' => 'clamp(8rem, 20vw, 20rem)',
                                    'height' => '25vw',
                                    'maxHeight' => '600px',
                                ],
                                1 => [
                                    'top' => '-5vw',
                                    'left' => '0vw',
                                    'font' => 'clamp(3rem, 15vw, 15rem)',
                                    'height' => '23vw',
                                    'maxHeight' => '560px',
                                ],
                                2 => [
                                    'top' => '-3vw',
                                    'left' => '0vw',
                                    'font' => 'clamp(2rem, 10vw, 10rem)',
                                    'height' => '23vw',
                                    'maxHeight' => '540px',
                                ],
                            ];
                            $medals = ['🥇', '🥈', '🥉'];
                        @endphp
                        @if (isset($topMovies))
                            @foreach ($topMovies as $index => $movie)
                                @php $s = $sizes[$index]; @endphp
                                <div class="col-10 col-md-3 text-center" style="position: relative;">

                                    {{-- Ranking Number --}}
                                    <div class="ranking-title"
                                        style="
                                    position: absolute;
                                    top: {{ $s['top'] }};
                                    left: {{ $s['left'] }};
                                    font-size: {{ $s['font'] }};
                                    line-height: 1;
                                    z-index: 1;
                                    background: linear-gradient(180deg, #fff 0%, #888 100%);
                                    -webkit-background-clip: text;
                                    -webkit-text-fill-color: transparent;
                                    font-weight: 900;
                                    text-shadow: none;
                                    filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.5));
                                ">
                                        {{ $index + 1 }}
                                    </div>

                                    {{-- card --}}
                                    <div class="card border-0"
                                        style="
                                    background: transparent;
                                    box-shadow: 0 8px 32px rgba(0,0,0,0.5);
                                    border-radius: 12px;
                                    overflow: hidden;
                                    transition: transform 0.3s;
                                "
                                        onmouseover="this.style.transform='translateY(-8px)'"
                                        onmouseout="this.style.transform='translateY(0)'">

                                        {{-- image --}}
                                        <div
                                            style="height: {{ $s['height'] }}; max-height: {{ $s['maxHeight'] }}; overflow: hidden; position: relative;">
                                            <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}"
                                                class="w-100 h-100" style="object-fit: cover;">
                                            {{-- gradation --}}
                                            <div
                                                style="
                                        position: absolute;
                                        bottom: 0; left: 0; right: 0;
                                        height: 60%;
                                        background: linear-gradient(transparent, rgba(0,0,0,0.7));
                                    ">
                                            </div>
                                            {{-- medal --}}
                                            <div style="position: absolute; top: 10px; right: 10px; font-size: 2rem;">
                                                {{ $medals[$index] }}
                                            </div>
                                        </div>

                                        {{-- title --}}
                                        <div class="p-2" style="background: rgba(8, 23, 41, 0.95);">
                                            <h5 class="text-white text-center mt-1 mb-1"
                                                style="font-size: clamp(0.8rem, 1.5vw, 1.1rem);">
                                                {{ $movie->title }}
                                            </h5>
                                            <p class="text-warning text-center mb-1" style="font-size: 0.85rem;">
                                                ⭐ {{ $movie->review_average }} &nbsp;·&nbsp;
                                                <span class="text-white-50">{{ floor($movie->duration / 60) }}h
                                                    {{ $movie->duration % 60 }}m</span>
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-white"> No images</p>
                        @endif



                    </div>
                </div>

            </div>
            <div class="container-fluid px-0 mt-5 section-gap" id="Nowplaying">
                <p class="display-3 text-white title-base ms-5">
                    🎬 Now Playing
                </p>

                <div class="w-100 py-4" style="background: rgba(16, 57, 133, 0.5);">

                    <!-- View All -->
                    <div class="d-flex justify-content-end px-4 mb-3">
                        <a href="#" class="view-all fs-5">View All Showtimes</a>
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
                                            <img src="{{ asset($movie->poster_url) }}" class="movie-poster w-100">
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
                                                <button class="book-btn mt-2 w-100">BOOK NOW</button>
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
            <div class="container-fluid px-0 mt-5 section-gap" id="Comingsoon">
                <p class="display-3 text-white title-base ms-5">
                    🎞️Coming Soon
                </p>

                <div style="background: rgba(16, 57, 133, 0.5)">

                    {{-- scroll --}}
                    <div class="position-relative px-3 pt-4">
                        <div class="d-flex gap-3 pb-2 mt-5 ms-4 me-5" id="comingSoonSlider"
                            style="overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none;">

                            @foreach ($comingSoonMovies as $movie)
                                <a href="#" class="text-decoration-none flex-shrink-0 m-4"
                                    style="scroll-snap-align: start; width: 400px;">

                                    <div style="overflow: hidden;">
                                        <img src="{{ asset($movie->poster_url) }}" alt="Movie"
                                            style="width: 100%; height: 360px; object-fit: cover; display: block;">
                                        <div class="p-2" style="background: rgba(255,255,255,0.85);">
                                            <p class="mb-0 text-dark text-center small">{{ $movie->title }}</p>
                                        </div>
                                    </div>
                                    <div>

                                        <p class="text-white text-center mt-2 ">
                                            Published on<br>
                                            <span class="display-5 text-white text-center fw-bold base">
                                                {{ \Carbon\Carbon::parse($movie->released_date)->format('j.n.Y') }}

                                            </span>
                                        </p>
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

    {{-- ===========================
         Location System Styles
         =========================== --}}
    <style>
        .location-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .location-dialog {
            width: 90%;
            max-width: 360px;
            background: #0d1b3d;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 32px 28px;
            text-align: center;
            color: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .location-dialog-icon {
            font-size: 2.2rem;
            color: #ffc107;
            margin-bottom: 12px;
        }

        .location-btn {
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
        }

        .location-btn-primary {
            background: #ffc107;
            color: #0d1b3d;
            border: none;
        }

        .location-btn-primary:hover {
            background: #e0a800;
            color: #0d1b3d;
        }

        .location-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .location-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .location-btn-outline {
            background: transparent;
            color: #adb5bd;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .location-btn-outline:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.35);
        }

        .location-change-btn {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .location-change-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .cinema-card {
            background: rgba(8, 23, 41, 0.9);
            border-radius: 10px;
            padding: 16px;
            height: 100%;
            color: #fff;
        }

        .cinema-card-name {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .cinema-card-meta {
            font-size: 0.82rem;
            color: #adb5bd;
            margin-bottom: 10px;
        }

        .cinema-card-distance {
            color: #ffc107;
            font-weight: 600;
        }
    </style>

    {{-- ===========================
         Location System Script
         =========================== --}}
    <script>
        (function () {
            const STORAGE_KEY = 'locationPermission';
            const overlay = document.getElementById('locationPermissionOverlay');
            const statusEl = document.getElementById('nearbyCinemasStatus');
            const listEl = document.getElementById('nearbyCinemasList');
            const changeBtn = document.getElementById('changeLocationPrefBtn');

            function showOverlay() {
                overlay.style.display = 'flex';
            }

            function hideOverlay() {
                overlay.style.display = 'none';
            }

            function showStatus(message, showSpinner) {
                statusEl.style.display = 'block';
                listEl.style.display = 'none';
                statusEl.innerHTML = (showSpinner ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>' : '') + message;
            }

            function renderCinemas(payload) {
                const cinemas = (payload && payload.cinemas) ? payload.cinemas : [];

                if (cinemas.length === 0) {
                    showStatus('No cinemas found nearby.', false);
                    return;
                }

                statusEl.style.display = 'none';
                listEl.style.display = 'flex';
                listEl.innerHTML = cinemas.map(function (cinema) {
                    const distance = cinema.distance_km !== null && cinema.distance_km !== undefined
                        ? '<div class="cinema-card-distance mb-2">' + cinema.distance_km + ' km away</div>'
                        : '';
                    const websiteBtn = cinema.maps_url
                        ? '<a href="' + cinema.maps_url + '" target="_blank" rel="noopener" class="btn btn-sm location-btn location-btn-primary w-100 mt-2">Visit Website</a>'
                        : '';

                    return '' +
                        '<div class="col-md-4 col-lg-3">' +
                            '<div class="cinema-card">' +
                                '<div class="cinema-card-name">' + escapeHtml(cinema.name) + '</div>' +
                                '<div class="cinema-card-meta">' + escapeHtml(cinema.address || '') + '</div>' +
                                distance +
                                websiteBtn +
                            '</div>' +
                        '</div>';
                }).join('');
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function fetchCinemas(lat, lng) {
                showStatus('Loading nearby theaters...', true);

                const params = new URLSearchParams();
                if (lat !== null && lng !== null) {
                    params.set('lat', lat);
                    params.set('lng', lng);
                }

                fetch('/api/nearby-cinemas?' + params.toString())
                    .then(function (res) { return res.json(); })
                    .then(renderCinemas)
                    .catch(function () {
                        showStatus('Could not load theaters right now.', false);
                    });
            }

            function fetchFallback() {
                fetchCinemas(null, null);
            }

            function requestLocationAndFetch() {
                if (!navigator.geolocation) {
                    fetchFallback();
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        fetchCinemas(position.coords.latitude, position.coords.longitude);
                    },
                    function () {
                        // Permission denied at browser level, or unavailable — fall back.
                        fetchFallback();
                    },
                    { timeout: 8000 }
                );
            }

            function handleChoice(choice) {
                if (choice === 'always') {
                    localStorage.setItem(STORAGE_KEY, 'always');
                    hideOverlay();
                    requestLocationAndFetch();
                } else if (choice === 'once') {
                    localStorage.removeItem(STORAGE_KEY);
                    hideOverlay();
                    requestLocationAndFetch();
                } else if (choice === 'deny') {
                    localStorage.setItem(STORAGE_KEY, 'deny');
                    hideOverlay();
                    fetchFallback();
                }
            }

            document.querySelectorAll('[data-choice]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    handleChoice(btn.getAttribute('data-choice'));
                });
            });

            changeBtn.addEventListener('click', function () {
                showOverlay();
            });

            const storedPref = localStorage.getItem(STORAGE_KEY);

            if (storedPref === 'always') {
                requestLocationAndFetch();
            } else if (storedPref === 'deny') {
                fetchFallback();
            } else {
                showOverlay();
            }
        })();
    </script>
@endsection