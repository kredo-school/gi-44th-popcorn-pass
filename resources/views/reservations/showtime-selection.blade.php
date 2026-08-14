@extends('layouts.app')
@section('title', 'Select Seat')
@section('content')


    <div class="reservation-page">
        <div class="date-slider-wrapper position-relative">
            <button class="slider-next">
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
                <img src="{{ $movie->poster_url }}" alt="movie-title" class="img-fluid">
            </div>
            <div class="col-5 blue-background ">
                <div>
                    <h1>{{ $movie->title }}</h1>
                    <div class="row">
                        <div class="col-1">
                            <p class="">
                            <p>{{ $movie->ageRating?->title ?? 'Not Rated' }}</p>
                            </p>
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

                    <div class="col-4 fw-bold">
                        CAST
                    </div>

                    <div class="col-8">
                        <div class="row">
                            @php
                                $castMembers = $movie->cast;

                                if (is_string($castMembers)) {
                                    $castMembers = json_decode($castMembers, true);

                                    if (!is_array($castMembers)) {
                                        $castMembers = array_filter(array_map('trim', explode(',', $movie->cast)));
                                    }
                                }

                                $castMembers = is_array($castMembers) ? $castMembers : [];
                            @endphp

                            @forelse ($castMembers as $castMember)
                                <div class="col-6">
                                    {{ $castMember }}
                                </div>
                            @empty
                                <div class="col-12">
                                    Not available
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="select-showtime mx-auto mt-5">

            <div class="mt-2 pt-3 blue-background-list">

                <div>
                    <h1 class="showtime-text pt-2 text-center">
                        『 Select a showtime 』<br>
                    </h1>

                </div>

                {{-- Sepalate Screen --}}
                @foreach ($movie->showtimes->groupBy('screen_id') as $screenId => $screenShowtimes)
                    @php
                        $screen = $screenShowtimes->first()->screen;
                    @endphp

                    {{-- title of Theater --}}
                    <div class="screen-title px-3 pt-3">
                        <hr class="text-white">
                        <h2 class="fs-4 text-white">
                            Theater {{ $screen->screen_number }} 【 {{ $screen->screen_type }} 】
                        </h2>
                    </div>

                    {{-- showtime of screen --}}
                    <div class="d-flex gap-3 showtime-list pb-2 ms-5">

                        @foreach ($screenShowtimes->sortBy('start_time') as $showtime)
                            @if ($showtime->start_time->isPast())
                                {{-- Closed --}}
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
                                                Theater
                                            </div>

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
                                {{-- Reservation --}}
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

                                    @auth

                                        {{-- already logged in --}}
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
                                    @else
                                        {{-- not logged in yet --}}
                                        <button type="button" class="showtime-reservation-btn border-0 bg-transparent p-0"
                                            data-bs-toggle="modal" data-bs-target="#guestLoginModal"
                                            data-showtime-id="{{ $showtime->id }}">

                                            <div class="showtime-bottom pt-2">
                                                <div class="reservation-icon">
                                                    ⭕️
                                                </div>

                                                <div class="reservation-text">
                                                    Reservation
                                                </div>
                                            </div>

                                        </button>

                                    @endauth

                                </div>
                            @endif
                        @endforeach

                    </div>
                @endforeach

            </div>

        </div>


        <div class="d-flex justify-content-between mt-5">
            <button type="button" class="back-btn ms-5" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> BACK
            </button>
        </div>

        @include('reservations.modals.guest-or-login')

    </div>


@endsection
