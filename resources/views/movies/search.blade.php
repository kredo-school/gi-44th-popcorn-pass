@extends('layouts.app')
@section('content')
    <div class="reservation-page" style="background-image: url('{{ asset('images/layouts/background.png') }}');">
        <h2 class="text-white text-center mb-4">Search Result</h2>

        <div class="container">
            @if ($movies->isEmpty())
                <p class="text-white text-center">No movies found.</p>
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