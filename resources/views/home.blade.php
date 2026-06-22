@extends('layouts.app')

@section('content')
    <div>
        <img src="{{ asset('storage/images/welcome.png') }}" alt="welcome.image" style="width: 100%; display: block;">
    </div>
    <div class="mt-0"
        style="
                background-image: url('{{ asset('storage/images/home_back.png') }}');
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
                                        <img src="{{ asset('storage/' . $movie->poster_url) }}" alt="{{ $movie->title }}"
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
                            <p class="text-white">データなし</p>
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
                        <a href="#" class="view-all fs-5">View All</a>
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
                                            <img src="{{ asset('storage/' . $movie->poster_url) }}"
                                                class="movie-poster w-100">
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
                                                        <span class="text-white-50" style="font-size:0.75rem;">Genre</span>
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
                                        <img src="{{ asset('storage/' . $movie->poster_url) }}" alt="Movie"
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
                    <img src="{{ asset('storage/images/foodmenu.png') }}" alt="foodmenu" class="w-100"
                        style="display: block; object-fit: cover;">
                </div>

            </div>

            <hr class="text-white">

            {{-- Information --}}
            <div class="container-fluid px-0 mt-5 section-gap" id="Information">
                <p class="display-3 text-white title-base ms-5 text-center mb-5">
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
                                    <img src="{{ asset('storage/images/news.png') }}" class="card-img-top"
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
        </div>


    </div>
    </div>
@endsection
