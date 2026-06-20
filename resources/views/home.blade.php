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
                <div class="  " style="background: rgba(16, 57, 133, 0.5)">
                    <div class="row justify-content-center align-items-end g-5 mt-2 pb-5">
                        {{-- 1 --}}
                        <div class="col-10 col-md-3 text-center" style="position: relative;">
                            <div class="ranking-title"
                                style="
                             position: absolute;
                             top: -6vw;
                             left: -1vw;
                             font-size: clamp(8rem, 20vw, 20rem);
                             line-height: 1;
                             z-index: 1;">
                                1
                            </div>
                            <div class="card">
                                <div class="card-header p-0 border-0"
                                    style="height: 25vw; max-height: 600px; overflow: hidden;">
                                    <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                                        class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="card-body back-gray p-2">
                                    <div class="card-text">
                                        <h5 class="text-dark base text-center mt-1">Pirates of caribbean</h5>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- 2 --}}
                        <div class="col-10 col-md-3 text-center" style="position: relative;">
                            <div class="ranking-title"
                                style="
                        position: absolute;
                        top: -5vw;
                        left: 0vw;
                        font-size: clamp(3rem, 15vw, 15rem);
                        line-height: 1;
                        z-index: 1;">
                                2
                            </div>
                            <div class="card">
                                <div class="card-header p-0 border-0"
                                    style="height: 23vw; max-height: 560px; overflow: hidden;">
                                    <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                                        class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="card-text back-gray p-2">
                                    <h5 class="text-dark base text-center mt-1">Pirates of caribbean</h5>
                                </div>
                            </div>
                        </div>

                        {{-- 3 --}}
                        <div class="col-10 col-md-3 text-center" style="position: relative;">
                            <div class="ranking-title"
                                style="
                        position: absolute;
                        top: -3vw;
                        left: 0vw;
                        font-size: clamp(2rem, 10vw, 10rem);
                        line-height: 1;
                        z-index: 1;">
                                3
                            </div>
                            <div class="card">
                                <div class="card-header p-0 border-0"
                                    style="height: 23vw; max-height: 540px; overflow: hidden;">
                                    <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                                        class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="card-text back-gray p-2">
                                    <h5 class="text-dark base text-center mt-1">Pirates of caribbean</h5>
                                </div>
                            </div>
                        </div>
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

                                @for ($i = 0; $i < 30; $i++)
                                    <div class="flex-shrink-0" style="scroll-snap-align: start; width: 200px;">
                                        <div class="movie-card">
                                            <img src="{{ asset('storage/images/movie.png') }}" class="movie-poster w-100">
                                            <div class="movie-info" style="background:#081729">
                                                <h6 class="text-white text-center mb-2 mt-2">
                                                    PIRATES OF CARIBBEAN
                                                </h6>
                                                <div class="movie-meta text-white small px-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-white-50">Adventure</span>
                                                        <span>2h 46m</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-white-50" style="font-size:0.75rem;">Genre</span>
                                                        <span>⭐4.2</span>
                                                    </div>
                                                </div>
                                                <button class="book-btn mt-2 w-100">BOOK NOW</button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor

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

                            @for ($i = 0; $i < 15; $i++)
                                <a href="#" class="text-decoration-none flex-shrink-0 m-4"
                                    style="scroll-snap-align: start; width: 400px;">

                                    <div style="overflow: hidden;">
                                        <img src="{{ asset('storage/images/movie.png') }}" alt="Movie"
                                            style="width: 100%; height: 360px; object-fit: cover; display: block;">
                                        <div class="p-2" style="background: rgba(255,255,255,0.85);">
                                            <p class="mb-0 text-dark text-center small">PIRATES OF CARIBBEAN</p>
                                        </div>
                                    </div>
                                    <div>

                                        <p class="text-white text-center mt-2 ">
                                            Published on<br>
                                            <span class="display-5 text-white text-center fw-bold base">10.7.2026</span>
                                        </p>
                                    </div>


                                </a>
                            @endfor

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
                <p class="display-3 text-white title-base ms-5 text-center">
                    📢 Information
                </p>
                {{-- <div class="container">
                    ...4列 = col-3...
                    <div class="row g-3">
                        @foreach ($foods as $food)
                            <div class="col-3"> 
                                <div class="card">
                                    <img src="{{ asset('storage/images/' . $food->image) }}" class="card-img-top"
                                        style="object-fit: cover; height: 200px;">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $food->name }}</h6>
                                        <p class="card-text">{{ $food->price }}円</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div> --}}



            </div>
        </div>


    </div>
    </div>
@endsection
