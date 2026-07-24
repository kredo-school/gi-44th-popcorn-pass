@extends('layouts.app')
@section('content')
    <div class="reservation-page " style="background-image: url('{{ asset('images/background.png') }}');">

        <div class="text-center">
            <div class="release-date">
                Movie Detail
            </div>

            <div class="release-sub">

            </div>
        </div>
        <div class="container movie-container ">
            <div class="mt-5 row text-white ">
                <div class="col-5 text-end ">
                    <img src="{{ $movie->poster_url }}" alt="movie-title" class="release-img">
                </div>
                <div class="col-5 blue-background ">
                    <div class="pt-3">
                        <div class="text-center">
                            <h1>{{ $movie->title }}</h1>

                            <div class="mt-1">
                                ※ {{ $movie->ageRating->title }}
                            </div>
                        </div>



                    </div>
                    <div class="synopsis-box mt-4 mb-3">
                        {{ $movie->synopsis }}
                    </div>
                    <div class="row p-3">
                        <div class="col-4 fw-bold">Review Average</div>
                        <div class="col-8">
                            <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                                class="text-decoration-none text-white text-small">

                                ⭐️
                                <span class=" ms-2">
                                    {{ number_format($averageRating, 1) }} / 5
                                </span>

                                <span class="review-total-count ms-2">
                                    ({{ number_format($totalReviews) }} reviews)
                                </span>

                            </a>
                        </div>




                        <div class="col-4 fw-bold">Genre</div>
                        <div class="col-8 ">
                            {{ $movie->genres->pluck('title')->join(', ') }}
                        </div>
                        <div class="col-4 fw-bold">RELEASE DATE</div>
                        <div class="col-8">
                            {{ \Carbon\Carbon::parse($movie->released_date)->format('Y-m-d') }}
                        </div>
                        <div class="col-4 fw-bold">END DATE</div>
                        <div class="col-8">
                            {{ \Carbon\Carbon::parse($movie->end_date)->format('Y-m-d') }}
                        </div>

                        <div class="col-4 fw-bold">RUN TIME</div>
                        <div class="col-8">{{ $movie->duration }} min</div>

                        <div class="col-4 fw-bold">DIRECTOR</div>
                        <div class="col-8">{{ $movie->director }}</div>

                        <div class="col-4 fw-bold">CAST</div>
                        <div class="col-8">{{ $movie->cast }}</div>
                    </div>
                    <a href="{{ $movie->trailer_url }}" target="_blank" class="text-trailer">
                        Watch the trailer
                    </a>
                    <div class="text-center mt-3 mb-3">
                        <a href="{{ route('reservations.showtime.selection', ['movie' => $movie->id]) }}">
                            <button class="book-btn mt-2 w-75">BOOK NOW</button>
                        </a>
                    </div>


                </div>
            </div>
        </div>





        {{-- UPDATE LATER --}}
        <div class="d-flex justify-content-between mt-5">
            <button type="button" class="back-btn ms-5" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> BACK
            </button>
        </div>


    </div>
@endsection
