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
    <div class="mt-0"
        style="
                background-image: url('{{ asset('images/home_back.png') }}');
                background-size: cover;
                background-position: center top;
                background-repeat: no-repeat;
                width: 100%;
            ">
        <div>{{-- SEARCH --}}

            <div class="search-wrapper w-50 container pt-5 mb-5">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" class="search-input mt-3" placeholder="Search movies, genres, or showtimes...">

                <button class="search-btn">
                    SEARCH
                </button>
            </div>
        </div>

        
    </div>
@endsection
