<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    //seat selection
    public function selectSeat()
    {
        return view('reservations.seat-selection');
    }


    //ticket type selecion
    public function ticket(Request $request)
    {
        $selectedSeats = json_decode($request->selectedSeats, true);

        return view('reservations.ticket-type', compact('selectedSeats'));
    }


    // payment method
    public function payment() {

    }


    // confirm
    public function confirmation() {

    }


    // complete
    public function complete() {}
}
