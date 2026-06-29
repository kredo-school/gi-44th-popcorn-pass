@extends('layouts.app')
@section('title', 'Reservation Confirm')
@section('content')


<div class="reservation-page">


    {{-- Stepper --}}
    <div class="stepper d-flex justify-content-center align-items-center mb-5">

        <div class="step complete">
            <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                1
            </div>
        </div>

        <div class="step-line"></div>

        <div class="step complete">
            <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                2
            </div>
        </div>

        <div class="step-line"></div>

        <div class="step complete">
            <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                3
            </div>
        </div>

        <div class="step-line"></div>

        <div class="step complete">
            <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                4
            </div>
        </div>

        <div class="step-line"></div>

        <div class="step current">
            <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                5
            </div>
        </div>

    </div>


    {{-- Main --}}
    <div class="text-center">
        <h3 class="confirm-title">Booking Confirmed!</h3>
        <p class="confirm-message">Your ticket is ready! Enjoy the Movie.</p>
    </div>

    <div class="row">

        {{-- Reservation Summary --}}
        <div class="col-6 mx-auto">
            <div class="card summary">
                <div class="card-header text-center border-0 ">
                    <h5 class="mb-0">YOUR SELECTION</h5>
                </div>

                <div class="card-body">
                    <img src="{{ asset('images/greatest-showman.jpg') }}" alt="Movie Poster"
                        class="img-fluid rounded mb-3">
                    <h5 class="fw-bold">
                        The Greatest Showman
                    </h5>
                    <hr>
                    <div class="mb-3">
                        <small class="">Screen</small>
                        <p class="mb-0 fw-bold">Screen 3</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small class="">Showtime</small>
                        <p class="mb-0 fw-bold">Jun 10, 2026 (Wed) 11:25 AM</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small>Seats</small>
                        <div id="selected-seats">
                            <div class="row">
                                @forelse($selectedSeats as $seat)
                                <div class="col-6 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="seat-tag {{ $seat['premium'] ? 'premium' : 'normal' }}">
                                            {{ $seat['seat'] }}
                                        </span>
                                        <p class="mb-0">{{ $seat['ticket'] }}</p>
                                        @if($seat['premium'])
                                        <p class="mb-0 text-warning fw-bold">(Premium +$10)</p>
                                        @endif
                                        <p class="mb-0 fw-bold">
                                            ${{ $seat['price'] + ($seat['premium'] ? 10 : 0) }}
                                        </p>
                                    </div>
                                </div>
                                @empty
                                <p>No seats selected</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small>Total Amount</small>
                        <p class="mb-0 fw-bold total-price">${{ $totalPrice }}</p>
                    </div>
                    <hr>

                    {{-- Update later(QR)  --}}
                    <div class="text-center my-4">
                        <div class="qr-placeholder">
                            QR CODE
                        </div>
                    </div>
                    
                    <p class="text-center booking-id">
                        Booking ID: 0000-0000
                    </p>
                    {{-- --- --}}

                </div>
            </div>
        </div>

    </div>

    {{-- Button !!!!UPDATE LATER!!!! --}}
    <div class="mt-5 text-center">
        <button id="go-to-mypage-btn" class="go-to-mypage-btn me-5" disabled>
            My Page<i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

</div>


@endsection