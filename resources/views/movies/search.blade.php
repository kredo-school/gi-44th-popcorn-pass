@extends('layouts.app')
@section('content')
    <div class="reservation-page" style="background-image: url('{{ asset('images/background.png') }}');">
        
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
        
        <div class="section-title-line mb-4 mt-5">
            <hr>
            <h2 class="text-white">
                < Search Result >
            </h2>
            <hr>
        </div>

        <div class="container">
            @if ($movies->isEmpty())
                <h1 class="text-white text-center">No movies found.</h1>
            @else
                <div class="search-result-grid">
                    @foreach ($movies as $movie)
                        <div class="search-card">
                            <img src="{{ $movie->poster_url }}" class="search-card-poster" alt="{{ $movie->title }}">

                            <div class="search-card-overlay">
                                <p class="search-card-title">{{ $movie->title }}</p>
                                <p class="search-card-director">{{ $movie->director }}</p>

                                <a href="{{ route('movie_detail', ['movie' => $movie->id]) }}" class="search-card-btn">
                                    Detail
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
