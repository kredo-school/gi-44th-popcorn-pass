@extends('layouts.app')
@section('title', 'Ticket Type')
@section('content')


<div class="seat-page">


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
        <div class="col-lg-8">
            <div class="ticket-card">
                <div class="d-flex align-items-start">
    
                    {{-- Seat Number --}}
                    <div class="seat-number-box premium">
                        E9
                    </div>
            
                    {{-- Ticket Type Area --}}
                    <div class="ticket-selection">
            
                        <button type="button" class="ticket-type-btn" data-bs-toggle="modal" data-bs-target="#ticketTypeModal">
                            SELECT TICKET TYPE
                        </button>
            
                        <div class="premium-text">
                            Premium Seat  + $10
                        </div>
                    </div>
                </div>
            </div>
            <div class="ticket-card mt-2">
                <div class="d-flex align-items-start">
            
                    {{-- Seat Number --}}
                    <div class="seat-number-box premium">
                        E9
                    </div>
            
                    {{-- Ticket Type Area --}}
                    <div class="ticket-selection">
            
                        <button type="button" class="ticket-type-btn" data-bs-toggle="modal" data-bs-target="#ticketTypeModal">
                            SELECT TICKET TYPE
                        </button>
            
                        <div class="premium-text">
                            
                        </div>
                    </div>
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
                        <div id="selected-seats">
                            <p>No seats selected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Button --}}
    <div class="d-flex justify-content-between mt-5">
        <button class="back-btn ms-5"><i class="fa-solid fa-arrow-left"></i>BACK</button>
        <button class="next-btn me-5">NEXT<i class="fa-solid fa-arrow-right"></i></button>
    </div>


</div>



@endsection