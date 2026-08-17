@extends('layouts.app')

@section('content')



    <div id="showtimes" class="pt-5 showtimes-bg">
        <div class="date-slider-wrapper">
            <div class="w-50 mx-auto">
                <div class="date-slider" id="dateSlider">
                    <button type="button" class="slider-next" id="dateSliderNextBtn">
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
                    data-bs-target="#nowShowing">
                    Now Showing
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

            <div class="tab-pane col-10 mx-auto fade {{ $isSearch ? '' : 'show active' }}" id="nowShowing">


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

                                {{-- ========================================
                                    TITLE
                                ======================================== --}}
                                <div class="mb-4">

                                    <div>
                                        <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}"
                                            class="showtime-movie-title text-decoration-none">
                                            {{ strtoupper($movie->title) }} >
                                        </a>
                                    </div>



                                </div>


                                {{-- ========================================
                                    SHOWTIME BY SCREEN TYPE
                                ======================================== --}}
                                @foreach ($movie->showtimes->groupBy('screen.screen_type') as $screenType => $screenTypeShowtimes)
                                    {{-- Screen Type Title --}}
                                    <div class="screen-type-title mb-2">

                                        <h3 class="fs-5">
                                            ----- Screen Type【 {{ $screenType }} 】-----

                                        </h3>

                                    </div>


                                    {{-- Showtime List --}}
                                    <div class="d-flex gap-3 flex-wrap showtime-list mb-4">

                                        @foreach ($screenTypeShowtimes->sortBy('start_time') as $showtime)
                                            {{-- ========================================
                                                CLOSED
                                            ======================================== --}}
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
                                                            <div class="theater-text">
                                                                Screen
                                                            </div>
                                                            <div class="theater-number theater-box">
                                                                {{ $showtime->screen->screen_number }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="showtime-bottom">
                                                        <div class="closed-icon">
                                                        </div>
                                                        <div class="closed-text">
                                                            Closed
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- ========================================
                                                    RESERVATION
                                                ======================================== --}}
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
                                                                Screen
                                                            </div>

                                                            <div class="theater-number theater-box">
                                                                {{ $showtime->screen->screen_number }}
                                                            </div>

                                                        </div>

                                                    </div>


                                                    {{-- Reservation --}}
                                                    <a href="{{ route('reservations.seat-selection', [
                                                        'showtime' => $showtime->id,
                                                        'new' => 1,
                                                    ]) }}"
                                                        class="text-decoration-none">

                                                        <div class="showtime-bottom pt-2">

                                                            <div class="reservation-icon">
                                                                ⭕️
                                                            </div>

                                                            <div class="reservation-text">
                                                                Reservation
                                                            </div>

                                                        </div>

                                                    </a>

                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                @empty
                    <h1 class="text-dark text-center mt-5">
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
                        < Popular Genres>
                    </p>

                    <div class="movie-search-genres-list text-decoration-none">

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Action']) }}#searchMovie"
                            class="movie-search-chip">
                            Action
                        </a>

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Adventure']) }}#searchMovie"
                            class="movie-search-chip">
                            Adventure
                        </a>

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Animation']) }}#searchMovie"
                            class="movie-search-chip">
                            Animation
                        </a>

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Comedy']) }}#searchMovie"
                            class="movie-search-chip">
                            Comedy
                        </a>

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Drama']) }}#searchMovie"
                            class="movie-search-chip">
                            Drama
                        </a>

                        <a href="{{ route('movies.search_showtime', ['keyword' => 'Sci-Fi']) }}#searchMovie"
                            class="movie-search-chip">
                            Sci-Fi
                        </a>

                    </div>

                </div>

                {{-- SEARCH RESULTS --}}
                @isset($searchResults)
                    <div class="container">

                        <div class="search-results-title">
                            <hr>

                            <h2>
                                < Search Results>
                            </h2>

                            <hr>
                        </div>


                        <div class="row">

                            @forelse($searchResults as $movie)
                                @continue($movie->showtimes->isEmpty())

                                <div class="movie-row p-4">

                                    <div class="row">

                                        {{-- ========================================
                                            MOVIE POSTER
                                        ======================================== --}}
                                        <div class="col-2 text-end mt-2">

                                            <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}">

                                                <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}"
                                                    class="img-showtime">

                                            </a>

                                        </div>


                                        {{-- ========================================
                                            RIGHT SIDE
                                        ======================================== --}}
                                        <div class="col-10">


                                            {{-- ========================================
                                                TITLE
                                            ======================================== --}}
                                            <div class="mb-4">

                                                <div>
                                                    <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}"
                                                        class="showtime-movie-title text-decoration-none">
                                                        {{ strtoupper($movie->title) }} >
                                                    </a>
                                                </div>

                                            </div>


                                            {{-- ========================================
                                                SHOWTIME BY SCREEN TYPE
                                            ======================================== --}}
                                            @foreach ($movie->showtimes->groupBy('screen.screen_type') as $screenType => $screenTypeShowtimes)
                                                {{-- Screen Type Title --}}
                                                <div class="screen-type-title mb-2">

                                                    <h3 class="fs-5">
                                                        ----- Screen Type【 {{ $screenType }} 】-----
                                                    </h3>

                                                </div>


                                                {{-- Showtime List --}}
                                                <div class="d-flex gap-3 flex-wrap showtime-list mb-4">

                                                    @foreach ($screenTypeShowtimes->sortBy('start_time') as $showtime)
                                                        {{-- ========================================
                                                            CLOSED
                                                        ======================================== --}}
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

                                                                        <div class="theater-text">
                                                                            Screen
                                                                        </div>

                                                                        <div class="theater-number theater-box">
                                                                            {{ $showtime->screen->screen_number }}
                                                                        </div>

                                                                    </div>

                                                                </div>


                                                                <div class="showtime-bottom">

                                                                    <div class="closed-icon">
                                                                    </div>

                                                                    <div class="closed-text">
                                                                        Closed
                                                                    </div>

                                                                </div>

                                                            </div>


                                                            {{-- ========================================
                                                                RESERVATION
                                                            ======================================== --}}
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
                                                                            Screen
                                                                        </div>

                                                                        <div class="theater-number theater-box">
                                                                            {{ $showtime->screen->screen_number }}
                                                                        </div>

                                                                    </div>

                                                                </div>


                                                                {{-- Reservation --}}
                                                                <a href="{{ route('reservations.seat-selection', [
                                                                    'showtime' => $showtime->id,
                                                                    'new' => 1,
                                                                ]) }}"
                                                                    class="text-decoration-none">

                                                                    <div class="showtime-bottom pt-2">

                                                                        <div class="reservation-icon">
                                                                            ⭕️
                                                                        </div>

                                                                        <div class="reservation-text">
                                                                            Reservation
                                                                        </div>

                                                                    </div>

                                                                </a>

                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>
                                            @endforeach

                                        </div>

                                    </div>

                                </div>


                            @empty

                                <p class="text-dark text-center">
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
