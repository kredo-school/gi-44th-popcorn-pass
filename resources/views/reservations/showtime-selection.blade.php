@extends('layouts.app')
@section('title', 'Select Seat')
@section('content')


    <div class="reservation-page">
        <div class="date-slider-wrapper position-relative">
            <button class="slider-next" onclick="scrollDateSlider()">
                ▶
            </button>

            <div class="w-50 mx-auto">
                <div class="date-slider" id="dateSlider-selection">

                    @foreach ($dates as $date)
                        @php
                            $selected = $selectedDate == $date->format('Y-m-d');
                            $isFuture = $loop->index >= 7;
                        @endphp

                        <a href="{{ route('reservations.showtime.selection', [
                            'movie' => $movie->id,
                            'date' => $date->format('Y-m-d'),
                        ]) }}"
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

        <div class="mt-5 row text-white ">
            <div class="col-5 text-end">
                <img src="{{ $movie->poster_url }}" alt="movie-title">
            </div>
            <div class="col-5 blue-background ">
                <div>
                    <h1>{{ $movie->title }}</h1>
                    <div class="row">
                        <div class="col-1">
                            <p class="">PG</p>
                        </div>
                        <div class="col-6">
                            <p>{{ $movie->genres->pluck('title')->join(', ') }}</p>
                        </div>
                    </div>


                    <div class="d-flex align-items-center gap-3 star-average">
                        <div>
                            @php
                                $rating = $movie->review_average;
                            @endphp

                            @for ($i = 1; $i <= 5; $i++)
                                @if ($rating >= $i)
                                    <i class="bi bi-star-fill rating-star "></i>
                                @elseif ($rating >= $i - 0.5)
                                    <i class="bi bi-star-half rating-star"></i>
                                @else
                                    <i class="bi bi-star rating-star"></i>
                                @endif
                            @endfor
                        </div>

                        <span>{{ number_format($movie->review_average, 1) }}/5 </span>
                        <a href="{{ route('reviews.index', ['movieId' => $movie->id]) }}"
                            class="text-decoration-none text-white">
                            <span class="ms-2">( {{ $movie->total_reviews }} reviews)</span>
                        </a>

                    </div>
                </div>
                <div class="synopsis-box mt-4 mb-3">
                    {{ $movie->synopsis }}
                </div>
                <div class="row p-3">
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

            </div>
        </div>
        <div class="select-showtime mx-auto mt-5">

            <div class="mt-2 blue-background-list ">
                <div>
                    <h1 class="showtime-text p-3">
                        『 Select a showtime 』<br>
                        <span class="fs-4">
                            Screen Type【 {{ $movie->showtimes->pluck('screen.screen_type')->unique()->implode(' / ') }} 】
                        </span>
                    </h1>
                </div>
                <div class="">

                </div>
                <!-- display showtime -->
                <div class="d-flex justify-content-center gap-3 flex-wrap showtime-list pb-5">

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
                                        <div class="theater-text">Theater</div>
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

        {{-- UPDATE LATER --}}
        <div class="d-flex justify-content-between mt-5">
            <button type="button" class="back-btn ms-5" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> BACK
            </button>

            <button type="submit" class="next-btn me-5" disabled>
                NEXT<i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>









    </div>



    <script>
        function scrollDateSlider() {
            document.getElementById('dateSlider-selection').scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        }
    </script>




@endsection
