@extends('layouts.app')
@section('title', 'Payent Method')
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

        <div class="step current">
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

        {{-- Payment method selection --}}
        <div class="col-lg-7">
            <div class="payment-panel">
            
                <h2 class="payment-title">
                    Choose Payment Method
                </h2>
            
                <div class="payment-options">
                
                    <button class="payment-btn {{ ($paymentInfo['method'] ?? 'card') === 'card' ? 'active' : '' }}" data-method="card">
                        Credit Card
                    </button>
                
                    <button class="payment-btn {{ ($paymentInfo['method'] ?? '') === 'paypal' ? 'active' : '' }}" data-method="paypal">
                        Paypal
                    </button>
                
                    <button class="payment-btn {{ ($paymentInfo['method'] ?? '') === 'onsite' ? 'active' : '' }}" data-method="onsite">
                        Pay On-Site
                    </button>
                
                </div>
                
                <div id="payment-form-container" class="mt-4">
                
                    <div id="card-form" class="payment-form {{ ($paymentInfo['method'] ?? 'card') !== 'card' ? 'd-none' : '' }}">
                        <input type="text" placeholder="Card Number" class="form-control mb-3"
                            value="{{ isset($paymentInfo['last4']) ? '**** **** **** ' . $paymentInfo['last4'] : '' }}">
                        <input type="text" placeholder="Card Holder Name" class="form-control mb-3">
                
                        <div class="row">
                            <div class="col">
                                <input type="text" placeholder="MM/YY" class="form-control">
                            </div>
                
                            <div class="col">
                                <input type="text" placeholder="CVV" class="form-control">
                            </div>
                        </div>
                    </div>
                
                    <div id="paypal-form" class="payment-form {{ ($paymentInfo['method'] ?? '') !== 'paypal' ? 'd-none' : '' }}">
                        <input type="email" placeholder="Paypal Email" class="form-control" value="{{ $paymentInfo['email'] ?? '' }}">
                    </div>
                
                    <div id="onsite-form" class="payment-form {{ ($paymentInfo['method'] ?? '') !== 'onsite' ? 'd-none' : '' }}">
                        <div class="alert alert-light mb-0">
                            <i class="fa-solid fa-triangle-exclamation"></i> You will pay at the cinema on the day of your visit.
                        </div>
                    </div>
                
                </div>
            
            </div>

            
            
        </div>

        {{-- Reservation Summary --}}
        <div class="col-lg-5">
            <div class="card summary">
                <div class="card-header text-center border-0 ">
                    <h5 class="mb-0">YOUR SELECTION</h5>
                </div>
        
                <div class="card-body">
                    <img src="{{ asset('images/greatest-showman.jpg') }}" alt="Movie Poster" class="img-fluid rounded mb-3">
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
        <form action="{{ route('reservations.ticket-type') }}" method="GET">
            <button type="button" class="back-btn ms-5" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> BACK
            </button>
        </form>

        <button id="next-btn" class="next-btn me-5" disabled>
            NEXT<i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

</div>

@endsection