<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // --------------------
    // Seat Selection Page
    // --------------------
    public function seatSelection()
    {
        $selectedSeats = session('selectedSeats', []);
        session()->forget('paymentInfo');

        return view('reservations.seat-selection', compact('selectedSeats'));
    }

    // --------------------
    // Seat Selection Store
    // --------------------
    public function seatSelectionStore(Request $request)
    {
        $selectedSeats = json_decode($request->selectedSeats ?? '[]', true);

        $selectedSeats = array_map(function ($seat) {
            return [
                'seat' => $seat['seat'],
                'premium' => $seat['premium'],
            ];
        }, $selectedSeats);

        session(['selectedSeats' => $selectedSeats]);
        session()->forget('paymentInfo');

        return redirect()->route('reservations.ticket-type');
    }

    // --------------------
    // Ticket Type Page
    // --------------------
    public function ticketType()
    {
        $selectedSeats = session('selectedSeats', []);

        if (empty($selectedSeats)) {
            return redirect()->route('reservations.seat-selection')
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;
            if (!empty($seat['premium'])) {
                $price += 10;
            }
            return $price;
        });

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
        session()->forget('paymentInfo');

        if ($request->clearPayment) {
            session()->forget('paymentInfo');
        }

        return response()->json(['status' => 'ok']);
    }

    // --------------------
    // Payment Method
    // --------------------
    public function paymentMethod()
    {
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);

        if (empty($selectedSeats)) {
            return redirect()->route('reservations.seat-selection')
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;

            if (!empty($seat['premium'])) {
                $price += 10;
            }

            return $price;
        });

        return view('reservations.payment-method', compact(
            'selectedSeats',
            'totalPrice',
            'paymentInfo'
        ));
    }

    // --------------------
    // Save Payment
    // --------------------
    public function savePayment(Request $request)
    {
        session(['paymentInfo' => [
            'method' => $request->method,
            'last4'  => $request->last4 ?? null,
            'email'  => $request->email ?? null,
        ]]);

        return response()->json(['status' => 'ok']);
    }

    // --------------------
    // Confirm
    // --------------------
    public function confirmation()
    {
        $selectedSeats = session('selectedSeats', []);
        $paymentInfo = session('paymentInfo', []);

        if (empty($selectedSeats) || empty($paymentInfo)) {
            return redirect()->route('reservations.seat-selection')
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;
            if (!empty($seat['premium'])) {
                $price += 10;
            }
            return $price;
        });

        return view('reservations.reservation-confirm', compact(
            'selectedSeats',
            'paymentInfo',
            'totalPrice'
        ));
    }

    // --------------------
    // Complete
    // --------------------
    public function complete()
    {
        $selectedSeats = session('selectedSeats', []);

        if (empty($selectedSeats)) {
            return redirect()->route('reservations.seat-selection')
                ->with('error', 'Your session has expired. Please start again.');
        }

        $totalPrice = collect($selectedSeats)->sum(function ($seat) {
            $price = $seat['price'] ?? 0;
            if (!empty($seat['premium'])) {
                $price += 10;
            }
            return $price;
        });

        session()->forget('selectedSeats');
        session()->forget('paymentInfo');

        return view('reservations.reservation-complete', compact('selectedSeats', 'totalPrice'));
    }
}
