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
            <hr class="text-dark">

            <h2 class="text-dark">
                &lt; Search Result &gt;
            </h2>

            <hr class="text-dark">
        </div>

        <div class="container">
            @if ($movies->isEmpty())
                <h1 class="text-dark text-center mt-5">No movies found.</h1>
            @else
                <div class="search-result-grid">
                    @foreach ($movies as $movie)
                        <div class="search-card">
                            <img src="{{ $movie->poster_url }}" class="search-card-poster" alt="{{ $movie->title }}">

                            <div class="search-card-overlay">
                                <p class="search-card-title text-center border">{{ $movie->title }}</p>

                                <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}"
                                    class="text-decoration-none">
                                    <button class="book-btn mt-2 w-100">
                                        BOOK NOW
                                    </button>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
