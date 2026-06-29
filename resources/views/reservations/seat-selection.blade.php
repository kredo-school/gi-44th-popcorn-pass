@extends('layouts.app')
@section('title', 'Select Seat')
@section('content')


    <div class="reservation-page">

        
        {{-- Stepper --}}
        <div class="stepper d-flex justify-content-center align-items-center mb-5">
    
            <div class="step current">
                <div class="step-circle d-flex justify-content-center align-items-center fw-semibold">
                    1
                </div>
            </div>
    
            <div class="step-line"></div>
    
            <div class="step upcoming">
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
        <form id="seat-form" action="{{ route('reservations.seat-selection.store') }}" method="POST">
            @csrf
        
            <input type="hidden" name="selectedSeats" id="selectedSeatsInput">
            <div id="seat-data" data-seats='@json($selectedSeats)'></div>

            <div class="row">
                <div class="fw-bolder fs-2 text-center text-white mb-5">SELECT YOUR SEAT</div>
        
                {{-- Seat Area --}}
                <div class="col-lg-8 seat-bg">
        
                    {{-- Screen --}}
                    <div class="screen w-75 mx-auto py-1 rounded text-center text-white bg-primary mb-4">
                        SCREEN
                    </div>
        
                    <div class="seat-map">
        
                        {{-- Seat Numbers --}}
                        <div class="seat-header d-flex justify-content-center align-items-center mb-3">
                            <span class="row-label"></span>
                                @for($seat = 1; $seat <= 12; $seat++) 
                                    <span class="seat-number text-center fw-bold">
                                        {{ $seat }}
                                    </span>
                                @endfor
                            <span class="row-label"></span>
                        </div>
        
                        {{-- Seat Rows --}}
                        @foreach(range('A', 'J') as $row)
        
                            {{-- Premium Seats Header --}}
                            @if($row == 'D')
                                <hr class="premium-divider">
                                <p class="text-center fw-bold text-black my-1">
                                    PREMIUM SEATS <span class="pay-extra">(+$10)</span>
                                </p>
                            @endif
            
                            <div class="seat-row d-flex justify-content-center align-items-center mb-2">
                                <span class="row-label fw-bold text-center">
                                    {{ $row }}
                                </span>
            
                                @for($seat = 1; $seat <= 12; $seat++)
                                    @php
                                        $isWheelchair=$row==='A' && in_array($seat, [1, 2, 11, 12]);
                                        $isPremium=in_array($row, ['D', 'E' ]);
                                    @endphp
                                    <button type="button" class="seat mx-1
                                                    {{ $isPremium ? 'premium' : 'available' }}
                                                    {{ $isWheelchair ? 'wheelchair' : '' }}" 
                                                    data-seat="{{ $row }}{{ $seat}}">
                                        @if($isWheelchair)
                                            <i class="fa-solid fa-wheelchair"></i>
                                        @endif
                                    </button>
                                @endfor
            
                                <span class="row-label fw-bold text-center">
                                    {{ $row }}
                                </span>
                            </div>
            
                            {{-- Premium Seats Footer --}}
                            @if($row == 'E')
                                <hr class="premium-divider mb-2">
                            @endif
        
                        @endforeach
        
                    </div>
        
                    {{-- Seat Legend --}}
                    <div class="d-flex justify-content-center gap-4 mt-4">
        
                        <div class="d-flex align-items-center">
                            <div class="legend-seat available me-2"></div>
                            <div class="legend-seat available premium me-2"></div>
                            <span>Available</span>
                        </div>
        
                        <div class="d-flex align-items-center">
                            <div class="legend-seat reserved me-2 bg-black"></div>
                            <span>Reserved</span>
                        </div>
        
                        <div class="d-flex align-items-center">
                            <div class="legend-seat selected me-2 bg-secondary"></div>
                            <span>Selected</span>
                        </div>
        
                    </div>
        
                </div>
        
                {{-- Reservation Summary --}}
                <div class="col-lg-4">
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
                                <small class="">Seats</small>
                                <div id="selected-seats"></div>
                            </div>
                            <div id="seat-limit-msg" class="text-danger mt-2 fw-bold fs-5" style="display:none;">
                                Maximum 6 seats can be selected.
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>

            {{-- Button !!!!UPDATE LATER!!!! --}}
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="back-btn ms-5">
                    <i class="fa-solid fa-arrow-left"></i>BACK
                </button>
            
                <button type="submit" class="next-btn me-5" disabled>
                    NEXT<i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

        </form>
        

    </div>
    


@endsection
