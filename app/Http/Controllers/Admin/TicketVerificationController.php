<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketVerificationController extends Controller
{
    /**
     * Display the ticket verification screen.
     */
    public function index(): View
    {
        return view('admin.tickets.verify');
    }

    /**
     * Find and verify a ticket from its QR token.
     */
    public function verify(Request $request): View
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:128'],
        ]);

        $ticket = Ticket::with([
            'reservationSeat.reservation.payment',
            'reservationSeat.reservation.movie',
            'reservationSeat.reservation.cinema',
            'reservationSeat.reservation.showtime',
            'reservationSeat.reservation.screen',
            'reservationSeat.showtimeSeat.screenSeat',
        ])
            ->where('qr_token', $validated['qr_token'])
            ->first();

        if (!$ticket) {
            return view('admin.tickets.verify', [
                'ticket' => null,
                'status' => 'invalid',
                'message' => 'Invalid ticket. No matching QR ticket was found.',
            ]);
        }

        $reservationSeat = $ticket->reservationSeat;
        $reservation = $reservationSeat?->reservation;
        $showtime = $reservation?->showtime;
        $payment = $reservation?->payment;

        if (!$reservation || !$showtime) {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'invalid',
                'message' => 'This ticket is missing reservation information.',
            ]);
        }

        if ($ticket->used_at !== null) {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'used',
                'message' => 'This ticket has already been used.',
            ]);
        }

        if ($reservation->reservation_status === 'cancelled') {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'cancelled',
                'message' => 'This reservation has been cancelled.',
            ]);
        }

        if ($reservation->reservation_status !== 'confirmed') {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'invalid',
                'message' => 'This reservation is not confirmed.',
            ]);
        }

        if (!$payment || $payment->payment_status !== 'paid') {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'unpaid',
                'message' => 'Payment for this ticket has not been completed.',
            ]);
        }

        if ($showtime->end_time && now()->greaterThan($showtime->end_time)) {
            return view('admin.tickets.verify', [
                'ticket' => $ticket,
                'status' => 'expired',
                'message' => 'This ticket has expired.',
            ]);
        }

        return view('admin.tickets.verify', [
            'ticket' => $ticket,
            'status' => 'valid',
            'message' => 'Valid ticket. Confirm the details before admitting the customer.',
        ]);
    }

    /**
     * Mark a valid ticket as used.
     */
    public function admit(Request $request, Ticket $ticket): RedirectResponse
    {
        $ticket->load([
            'reservationSeat.reservation.payment',
            'reservationSeat.reservation.showtime',
        ]);

        $reservation = $ticket->reservationSeat?->reservation;
        $showtime = $reservation?->showtime;
        $payment = $reservation?->payment;

        if ($ticket->used_at !== null) {
            return redirect()
                ->route('admin.tickets.verify.index')
                ->with('error', 'This ticket has already been used.');
        }

        if (!$reservation || !$showtime) {
            return redirect()
                ->route('admin.tickets.verify.index')
                ->with('error', 'Invalid ticket information.');
        }

        if ($reservation->reservation_status !== 'confirmed') {
            return redirect()
                ->route('admin.tickets.verify.index')
                ->with('error', 'This reservation is not confirmed.');
        }

        if (!$payment || $payment->payment_status !== 'paid') {
            return redirect()
                ->route('admin.tickets.verify.index')
                ->with('error', 'Payment for this ticket has not been completed.');
        }

        if ($showtime->end_time && now()->greaterThan($showtime->end_time)) {
            return redirect()
                ->route('admin.tickets.verify.index')
                ->with('error', 'This ticket has expired.');
        }

        $ticket->update([
            'used_at' => now(),
        ]);

        return redirect()
            ->route('admin.tickets.verify.index')
            ->with('success', 'Ticket admitted successfully.');
    }
}