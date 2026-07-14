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
            <div class="carousel-item">

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

    </div>

    <div id="showtƒimes" class="pt-5 "
        style="
                background-image: url('{{ asset('images/home_back.png') }}');
                background-size: cover;
                background-position: center top;
                background-repeat: no-repeat;
                width: 100%;
            ">
        <div class="date-slider-wrapper">
            <div class="w-50 mx-auto">
                <div class="date-slider" id="dateSlider">
                    <button class="slider-next" onclick="scrollDateSlider()">
                        ▶
                    </button>


                    @foreach ($dates as $date)
                        @php
                            $selected = $selectedDate == $date->format('Y-m-d');
                            $isFuture = $loop->index >= 7;
                        @endphp

                        <a href="{{ route('movie.showtime.display', ['date' => $date->format('Y-m-d')]) }}#showtimes"
                            class="date-item
                            {{ $selected ? 'active' : '' }}
                            {{ $isFuture ? 'date-disabled' : '' }}">

                            <div class="date-day">
                                {{ $date->format('n/j') }}
                            </div>

                            <div class="date-week">
                                ({{ $date->format('D') }})
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

        <ul class="nav nav-tabs justify-content-center mt-5">
            <li class="nav-item">
                <button class="nav-link movie-tab {{ $isSearch ? '' : 'active' }}" data-bs-toggle="tab"
                    data-bs-target="#nowPlaying">
                    Now Playing
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link movie-tab {{ $isSearch ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#searchMovie">
                    Searching Movie
                </button>
            </li>
        </ul>

        <div class="tab-content showtime-bg row">

            {{-- showtime schedule --}}

            <div class="tab-pane col-10 mx-auto fade {{ $isSearch ? '' : 'show active' }}" id="nowPlaying">


                @php
                    $visibleMovies = $movies->filter(fn($movie) => $movie->showtimes->isNotEmpty());
                @endphp

                @forelse ($visibleMovies as $movie)
                    <div class="movie-row p-4">

                        <div class="row">

                            <!-- movie poster images -->
                            <div class="col-2 text-end mt-2">
                                <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                    <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}"
                                        class="img-showtime">
                                </a>
                            </div>

                            <!-- Right side -->
                            <div class="col-10">

                                <!-- title -->
                                <div class="mb-4">
                                    <div>
                                        <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}"
                                            class="showtime-movie-title text-decoration-none">
                                            {{ strtoupper($movie->title) }} >
                                        </a>
                                    </div>

                                    <div class="">
                                        <p>
                                           Screen Type【 {{ $movie->showtimes->pluck('screen.screen_type')->unique()->implode(' / ') }} 】
                                        </p>
                                    </div>
                                </div>


                                <!-- display showtime -->
                                <div class="d-flex gap-3 flex-wrap showtime-list">

                                    @foreach ($movie->showtimes->sortBy('start_time') as $showtime)
                                        @if ($showtime->start_time->isPast())
                                            <div class="showtime-card-closed">
                                                <div class="showtime-top">
                                                    <div class="showtime-time">
                                                        {{ $showtime->start_time->format('H:i') }}
                                                        <div class="showtime-end">
                                                            ～{{ $showtime->end_time->format('H:i') }}
                                                        </div>
                                                    </div>
                                                    <div class="ms-2">
                                                        <div class="theater-text">Screen</div>
                                                        <div class="theater-number theater-box">
                                                            {{ $showtime->screen->screen_number }}
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="showtime-bottom">
                                                    <div class="closed-icon">✖️</div>
                                                    <div class="closed-text">Closed</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="showtime-card-reservation">
                                                <div class="showtime-top">
                                                    <div class="showtime-time">
                                                        {{ $showtime->start_time->format('H:i') }}
                                                        <div class="showtime-end">
                                                            ～{{ $showtime->end_time->format('H:i') }}
                                                        </div>
                                                    </div>
                                                    <div class="ms-2">
                                                        <div class="theater-text">Theater</div>
                                                        <div class="theater-number theater-box">
                                                            {{ $showtime->screen->screen_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="{{ route('reservations.seat-selection', ['showtime' => $showtime->id]) }}"
                                                    class="text-decoration-none">

                                                    <div class="showtime-bottom pt-2">
                                                        <div class="reservation-icon">⭕️</div>
                                                        <div class="reservation-text">Reservation</div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <h1 class="text-white text-center mt-5">
                        No movies available for this date.
                    </h1>
                @endforelse

            </div>





            {{-- searching movie --}}
            <div class="tab-pane fade {{ $isSearch ? 'show active' : '' }}" id="searchMovie">
                {{-- SEARCH FORM --}}
                <div class="">
                    <form action="{{ route('movies.search_showtime') }}#searchMovie" method="GET">

                        <div class="movie-search-wrapper">

                            <div class="movie-search-box">

                                <i class="fa-solid fa-magnifying-glass movie-search-icon"></i>

                                <input type="text" name="keyword" class="movie-search-input"
                                    placeholder="Search movies..." value="{{ request('keyword') }}">

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

                    <div class="movie-search-genres-list text-decoration-none">

                        <a href="{{ route('movies.search', ['keyword' => 'Action']) }}#searchMovie"
                            class="movie-search-chip">Action</a>
                        <a href="{{ route('movies.search', ['keyword' => 'Adventure']) }}#searchMovie"
                            class="movie-search-chip">Adventure</a>
                        <a href="{{ route('movies.search', ['keyword' => 'Animation']) }}#searchMovie"
                            class="movie-search-chip">Animation</a>
                        <a href="{{ route('movies.search', ['keyword' => 'Comedy']) }}#searchMovie"
                            class="movie-search-chip">Comedy</a>
                        <a href="{{ route('movies.search', ['keyword' => 'Drama']) }}#searchMovie"
                            class="movie-search-chip">Drama</a>
                        <a href="{{ route('movies.search', ['keyword' => 'Sci-Fi']) }}#searchMovie"
                            class="movie-search-chip">Sci-Fi</a>

                    </div>

                </div>

                {{-- SEARCH RESULTS --}}
                @isset($searchResults)
                    <div class="container">

                        <h3 class="text-white mb-4">
                            Search Results
                        </h3>

                        <div class="row">

                            @forelse($searchResults as $movie)
                                @continue($movie->showtimes->isEmpty())
                                <div class="movie-row p-4">

                                    <div class="row">

                                        <!-- movie poster images -->
                                        <div class="col-2 text-end mt-2">
                                            <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">
                                                <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}"
                                                    class="img-showtime">
                                            </a>
                                        </div>

                                        <!-- Right side -->
                                        <div class="col-10">

                                            <!-- title -->
                                            <div class="mb-4">
                                                <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}"
                                                    class="showtime-movie-title text-decoration-none">
                                                    {{ strtoupper($movie->title) }} >
                                                </a>
                                            </div>

                                            <!-- display movies -->
                                            <div class="d-flex gap-3 flex-wrap showtime-list">

                                                @foreach ($movie->showtimes->sortBy('start_time') as $showtime)
                                                    @if ($showtime->start_time->isPast())
                                                        <div class="showtime-card-closed">

                                                            <div class="showtime-top">
                                                                <div class="showtime-time">
                                                                    {{ $showtime->start_time->format('H:i') }}

                                                                    <div class="showtime-end">
                                                                        ～{{ $showtime->end_time->format('H:i') }}
                                                                    </div>
                                                                </div>

                                                                <div class="ms-2 ">
                                                                    <div class="theater-text">
                                                                        Theater
                                                                    </div>

                                                                    <div class="theater-number theater-box">
                                                                        {{ $showtime->screen->screen_number }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="showtime-bottom ">
                                                                <div class="closed-icon">✖️</div>
                                                                <div class="closed-text ">
                                                                    Closed
                                                                </div>
                                                            </div>


                                                        </div>
                                                    @else
                                                        <div class="showtime-card-reservation">

                                                            <div class="showtime-top">
                                                                <div class="showtime-time">
                                                                    {{ $showtime->start_time->format('H:i') }}

                                                                    <div class="showtime-end">
                                                                        ～{{ $showtime->end_time->format('H:i') }}
                                                                    </div>
                                                                </div>

                                                                <div class="ms-2">
                                                                    <div class="theater-text">
                                                                        Theater
                                                                    </div>

                                                                    <div class="theater-number theater-box">
                                                                        {{ $showtime->screen->screen_number }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="{{ route('reservations.showtimeSelection', ['showtime' => $showtime->id]) }}"
                                                                class="text-decoration-none">
                                                                <div class="showtime-bottom pt-2">
                                                                    <div class="reservation-icon">⭕️</div>
                                                                    <div class="reservation-text ">
                                                                        Reservation
                                                                    </div>
                                                                </div>
                                                            </a>


                                                        </div>
                                                    @endif
                                                @endforeach
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


    <script>
        function scrollDateSlider() {
            document.getElementById('dateSlider').scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        }
    </script>

@endsection
