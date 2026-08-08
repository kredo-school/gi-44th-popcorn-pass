@extends('layouts.admin')

@section('title', 'Reservation Management')
@section('page-title', 'Reservation Management')

@section('content')

    <form method="GET" action="{{ route('admin.reservations') }}" class="d-flex gap-2 mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search reservations..." style="max-width: 250px;" value="{{ request('search') }}">

        <select name="status" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Status: All</option>
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <select name="cinema_id" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('cinema_id', 'all') == 'all' ? 'selected' : '' }}>Cinema: All</option>
            @foreach ($cinemas as $cinema)
                <option value="{{ $cinema->id }}" {{ request('cinema_id') == $cinema->id ? 'selected' : '' }}>{{ $cinema->cinema_name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-outline-warning">Search</button>

        <div class="ms-auto">
            <a href="{{ route('admin.reservations.export', request()->query()) }}" class="btn btn-outline-warning">Export CSV</a>
        </div>
    </form>

    <div class="row g-3">
        <div class="col-md-8">
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
                            <tr class="reservation-row" data-reservation-id="{{ $reservation->id }}" style="cursor: pointer;">
                                <td>{{ $reservation->reservation_reference ?? '—' }}</td>
                                <td>{{ $reservation->user->username ?? '—' }}</td>
                                <td>{{ $reservation->movie->title ?? '—' }}</td>
                                <td>{{ $reservation->cinema->cinema_name ?? '—' }}</td>
                                <td>{{ $reservation->screen->screen_number ?? '—' }}</td>
                                <td>{{ $reservation->seat_numbers->implode(', ') ?: '—' }}</td>
                                <td>${{ number_format($reservation->final_amount, 2) }}</td>
                                <td>
                                    @php
                                        $paymentStatus = $reservation->payment->payment_status ?? 'unpaid';
                                    
                                        $paymentBadgeClass = match ($paymentStatus) {
                                        'paid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'failed' => 'bg-danger',
                                        'refunded' => 'bg-info text-dark',
                                        default => 'bg-secondary',
                                        };
                                    @endphp
                                
                                    <span class="badge {{ $paymentBadgeClass }}">
                                        {{ ucfirst($paymentStatus) }}
                                    </span>
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
        </div>

        <div class="col-md-4">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-3">Reservation Details</div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Booking ID</label>
                    <div class="form-control bg-transparent text-white" id="detail-booking-id">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Customer</label>
                    <div class="form-control bg-transparent text-white" id="detail-customer">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Movie / Cinema / Screen</label>
                    <div class="form-control bg-transparent text-white" id="detail-movie-cinema">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Showtime</label>
                    <div class="form-control bg-transparent text-white" id="detail-showtime">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Seats</label>
                    <div class="form-control bg-transparent text-white" id="detail-seats">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Subtotal / Discount / Final</label>
                    <div class="form-control bg-transparent text-white" id="detail-amount">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Payment</label>
                    <div class="form-control bg-transparent text-white" id="detail-payment">—</div>
                </div>

                {{-- On-Site Payment Action --}}
                <div id="detail-payment-action" class="mb-3 d-none" data-mark-paid-url-template="{{ route(
                        'admin.payments.mark-paid',
                        ['payment' => '__PAYMENT_ID__']
                    ) }}" data-csrf-token="{{ csrf_token() }}">
                
                    <button type="button" id="mark-payment-paid-btn" class="btn btn-success w-100">
                
                        <i class="fa-solid fa-check me-1"></i>
                        Mark as Paid
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Reservation Status</label>
                    <div class="form-control bg-transparent text-white" id="detail-status">—</div>
                </div>

                <div class="mb-0">
                    <label class="form-label text-secondary small">QR Code</label>
                    <div class="form-control bg-transparent text-white" id="detail-qr" style="word-break: break-all;">—</div>
                </div>
            </div>
        </div>
    </div>

@endsection

