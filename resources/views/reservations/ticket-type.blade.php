@extends('layouts.app')
@section('title', 'Ticket Type')
@section('content')


    <div class="reservation-page" id="ticket-data" data-seats='@json($selectedSeats)'>

        {{-- Stepper --}}
        <div class="stepper d-flex justify-content-center align-items-center mb-5">

            <div class="step complete">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    1
                </div>
            </div>

            <div class="step-line"></div>

            <div class="step current">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    2
                </div>
            </div>

            <div class="step-line"></div>

            <div class="step upcoming">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    3
                </div>
            </div>

            <div class="step-line"></div>

            <div class="step upcoming">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    4
                </div>
            </div>

            <div class="step-line"></div>

            <div class="step upcoming">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    5
                </div>
            </div>

        </div>


        {{-- Main --}}
        <div class="row">

            {{-- Ticket Type Selection --}}
            <div class="col-lg-7">

                @foreach ($selectedSeats as $seat)
                    <div class="ticket-card mb-3">
                        <div class="d-flex align-items-start">
                            <div class="seat-number-box {{ $seat['premium'] ? 'premium' : 'regular' }}">
                                {{ $seat['seat'] }}
                            </div>

                            <div class="ticket-selection">
                                <button type="button" class="ticket-type-btn {{ $seat['ticket'] ?? '' ? 'selected' : '' }}"
                                    data-bs-toggle="modal" data-bs-target="#ticketTypeModal" data-seat="{{ $seat['seat'] }}"
                                    data-premium="{{ $seat['premium'] }}">
                                    @if (isset($seat['ticket']))
                                        <span class="ticket-name">{{ $seat['ticket'] }}</span>
                                        <span class="ticket-price">${{ $seat['price'] }}</span>
                                    @else
                                        SELECT TICKET TYPE
                                    @endif
                                </button>

                                @if ($seat['premium'])
                                    <div class="premium-text">
                                        Premium Seat + $10
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- Reservation Summary --}}
            <div class="col-lg-5">
                <div class="card summary">
                    <div class="card-header text-center border-0 ">
                        <h5 class="mb-0">YOUR SELECTION</h5>
                    </div>

                    <div class="card-body">

                        <div class="text-center">
                            <img src="{{ $showtime->movie->poster_url }}" alt="Movie Poster" class="reservation-img">
                        </div>
                        <h5 class="fw-bold text-center mt-3">
                            {{ $showtime->movie->title }}
                        </h5>
                        <hr>
                        <div class="mb-3">
                            <small class="">Screen</small>

                            <p class="mb-0 fw-bold fs-5">{{ $showtime->screen->screen_number }}</p>

                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="">Showtime</small>

                            <p class="mb-0 fw-bold fs-5">
                                {{ $showtime->start_time->format('F j, Y | H:i') }}
                            </p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="">Seats</small>
                            <div id="selected-seats">
                                @foreach ($selectedSeats as $seat)
                                    <span class="seat-tag {{ $seat['premium'] ? 'premium' : 'normal' }}">
                                        {{ $seat['seat'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="fd-5">Total Amount</small>
                            <p class="mb-0 fw-bold total-price">
                                ${{ $totalPrice }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-between mt-5">

            
            <form action="{{ route('reservations.showtimeSelection', ['showtime' => session('showtime_id')]) }}" method="GET">
                <button  type="button" class="back-btn ms-5" onclick="history.back()">
                    <i class="fa-solid fa-arrow-left"></i> BACK
                </button>
            </form>

            <button type="" id="next-btn" class="next-btn me-5" disabled>

                NEXT<i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>

    </div>

    @include('reservations.modals.ticket-type-selection')

@endsection
