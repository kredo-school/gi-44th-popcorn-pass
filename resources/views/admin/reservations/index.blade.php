@extends('layouts.admin')

@section('title', 'Reservation Management')
@section('page-title', 'Reservation Management')

@section('content')

    <div class="d-flex gap-2 mb-3">
        <input type="text" class="form-control" placeholder="Search reservations..." style="max-width: 250px;">
        <select class="form-select" style="max-width: 150px;">
            <option>Status: All</option>
        </select>
        <select class="form-select" style="max-width: 150px;">
            <option>Cinema: All</option>
        </select>
        <div class="ms-auto">
            <a href="#" class="btn btn-outline-warning">Export CSV</a>
        </div>
    </div>

    <div class="card card-dark p-3">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Movie</th>
                    <th>Cinema</th>
                    <th>Screen</th>
                    <th>Seats</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->reservation_reference ?? '—' }}</td>
                        <td>{{ $reservation->user->username ?? '—' }}</td>
                        <td>{{ $reservation->movie->title ?? '—' }}</td>
                        <td>{{ $reservation->cinema->cinema_name ?? '—' }}</td>
                        <td>{{ $reservation->screen->screen_number ?? '—' }}</td>
                        <td>{{ $reservation->total_seats }}</td>
                        <td>${{ number_format($reservation->final_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $reservation->payment->payment_status ?? 'unpaid' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $reservation->reservation_status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-secondary py-4">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $reservations->links() }}
    </div>

@endsection