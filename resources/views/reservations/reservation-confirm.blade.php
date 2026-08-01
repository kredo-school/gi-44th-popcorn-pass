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

            <div class="step current">
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

            {{-- Reservation Summary --}}
            <div class="col-6 mx-auto">
                <div class="card summary">
                    <div class="card-header text-center border-0 ">
                        <h5 class="mb-0">YOUR SELECTION</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="text-center">
                                <img src="{{ $showtime->movie->poster_url }}" alt="Movie Poster" class="reservation-img ">
                            </div>
                            <h3 class="fw-bold text-center mt-3">
                                {{ $showtime->movie->title }}
                            </h3>

                        </div>



                        <hr>
                        <div class="mb-3">
                            <small class="">Screen</small>
                            <p class="mb-0 fw-bold fs-5">{{ $showtime->screen->screen_number }}</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="">Showtime</small>
                            <p class="mb-0 fw-bold fs-5">{{ $showtime->start_time->format('F j, Y | H:i') }}</p>
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
                                                @if ($seat['premium'])
                                                    <p class="mb-0 premium-text fw-bold">(+$10)</p>
                                                @endif
                                                <p class="mb-0 fw-bold fs-5">
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

                        @guest
                            <div class="mb-3">
                                <small>Guest Information</small>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Name</span>
                                        <span class="fw-bold">
                                            {{ $guestInfo['first_name'] ?? '' }}
                                            {{ $guestInfo['last_name'] ?? '' }}
                                        </span>
                                    </div>
                            
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Email</span>
                                        <span class="fw-bold">
                                            {{ $guestInfo['email'] ?? '-' }}
                                        </span>
                                    </div>
                            
                                    <div class="d-flex justify-content-between">
                                        <span>Phone Number</span>
                                        <span class="fw-bold">
                                            {{ $guestInfo['phone'] ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endguest

                        <div class="mb-3">
                            <small>Payment Method</small>
                            <p class="mb-0 fw-bold fs-5">
                                @if (($paymentInfo['payment_method'] ?? '') === 'paypal')
                                    PayPal ({{ $paymentInfo['email'] }})
                                @elseif (($paymentInfo['payment_method'] ?? '') === 'onsite')
                                    Pay On-Site
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Subtotal</span>
                                <span class="fs-5">${{ number_format($subtotal, 2) }}</span>
                            </div>
                        
                            @if($promotionDiscount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Promotion Discount</span>
                                    <span class="fs-5">-${{ number_format($promotionDiscount, 2) }}</span>
                                </div>
                            @endif
                        
                            @if($couponDiscount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Coupon Discount</span>
                                    <span class="fs-5">-${{ number_format($couponDiscount, 2) }}</span>
                                </div>
                            @endif

                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-4">Total</span>
                                <span class="fw-bold total-price">
                                    ${{ number_format($totalPrice, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-between mt-5">
            <form action="{{ route('reservations.payment-method') }}" method="GET">
                <button type="button" class="back-btn ms-5" onclick="history.back()">
                    <i class="fa-solid fa-arrow-left"></i> BACK
                </button>
            </form>

            <form action="{{ route('reservations.confirm-booking') }}" method="POST">

                @csrf

                <button type="submit" class="confirm-btn me-5">
                    Confirm Booking
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

            </form>
        </div>

    </div>


@endsection
