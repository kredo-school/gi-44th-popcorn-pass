<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // --------------------
    // Seat Selection Page
    // --------------------
    public function seatSelectionPage()
    {
        return view('reservations.seat-selection');
    }

    // --------------------
    // Seat Selection Store
    // --------------------
    public function seatSelectionStore(Request $request)
    {
        $selectedSeats = json_decode($request->selectedSeats ?? '[]', true);

        session(['selectedSeats' => $selectedSeats]);

        return redirect()->route('reservations.ticket-type');
    }

    // --------------------
    // Ticket Type Page
    // --------------------
    public function ticketType()
    {
        $selectedSeats = session('selectedSeats', []);

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            return (int)($seat['price'] ?? 0);
        }) + collect($selectedSeats)->where('premium', true)->count() * 10;

        return view('reservations.ticket-type', compact(
            'selectedSeats',
            'totalPrice'
        ));
    }

    // --------------------
    // Save Ticket
    // --------------------
    public function saveTicket(Request $request)
    {
        $selectedSeats = is_string($request->seats)
            ? json_decode($request->seats, true)
            : $request->seats;

        session(['selectedSeats' => $selectedSeats]);

        return response()->json(['status' => 'ok']);
    }

    // --------------------
    // Payment Method
    // --------------------
    public function paymentMethod()
    {
        $selectedSeats = session('selectedSeats', []);

        return view('reservations.payment-method', compact('selectedSeats'));
    }

    // --------------------
    // Confirm
    // --------------------
    public function confirmation()
    {
        $selectedSeats = session('selectedSeats', []);

        return view('reservations.reservation-confirm', compact('selectedSeats'));
    }

    // --------------------
    // Complete
    // --------------------
    public function complete()
    {
        session()->forget('selectedSeats');

        return view('reservations.reservation-complete');
    }
}
