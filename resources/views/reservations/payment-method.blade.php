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
        <form action="{{ route('reservations.confirm') }}" method="POST">
            @csrf
        
            <div class="row">
        
                {{-- Payment method selection --}}
                <div class="col-lg-7">
                    <div class="payment-panel">
        
                        {{-- Guest Information --}}
                        @guest
                            <div class="guest-info-panel mb-5">
                                <h2 class="payment-title">
                                    Guest Information
                                </h2>

                                <div class="row align-items-center mb-3">
                                    <label class="col-sm-2 col-form-label fw-bold text-end">
                                        Name<span class="text-danger">*</span>
                                    </label>
                                
                                    <div class="col-sm-9">
                                        <div class="row g-2">
                                            <div class="col">
                                                <input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name">
                                            </div>
                                
                                            <div class="col">
                                                <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="row align-items-center mb-3">
                                    <label for="guest_email" class="col-sm-2 col-form-label fw-bold text-end">
                                        Email<span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="email" id="guest_email" name="guest_email" class="form-control" placeholder="Enter your email">
                                    </div>
                                </div>
                                
                                <div class="row align-items-center">
                                    <label for="guest_phone" class="col-sm-2 col-form-label fw-bold text-end">
                                        Phone Number<span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="guest_phone" name="guest_phone" class="form-control"
                                            placeholder="Enter your phone number">
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endguest
        
        
                        <h2 class="payment-title">
                            Choose Payment Method
                        </h2>
                        <div class="payment-options">
        
                            <button type="button"
                                class="payment-btn {{ ($paymentInfo['payment_method'] ?? '') === 'paypal' ? 'active' : '' }}"
                                data-method="paypal">
                                Paypal
                            </button>
        
                            <button type="button"
                                class="payment-btn {{ ($paymentInfo['payment_method'] ?? '') === 'onsite' ? 'active' : '' }}"
                                data-method="onsite">
                                Pay On-Site
                            </button>
                        </div>
        
                        {{-- hidden payment method --}}
                        <input type="hidden" name="payment_method" id="payment_method">
        
                        <div id="payment-form-container" class="mt-4">
                                
                            <div id="paypal-form" class="payment-form d-none">
                                <input type="email" name="paypal_email" class="form-control" placeholder="PayPal Email">
                            </div>
    
                            <div id="onsite-form" class="payment-form d-none">
                                <div class="alert alert-danger mb-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    You will pay at the cinema on the day of your visit.
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
                                <small class="fd-5">Total</small>
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
                <button type="button" class="back-btn ms-5" onclick="history.back()">
                    <i class="fa-solid fa-arrow-left"></i>
                    BACK
                </button>
        
                <button type="submit" id="next-btn" class="next-btn me-5">
                    NEXT
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        
        </form>

    </div>

@endsection
