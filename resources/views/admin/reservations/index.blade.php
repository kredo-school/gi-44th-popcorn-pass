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

@section('scripts')
<script>
document.querySelectorAll('.reservation-row').forEach(function (row) {
    row.addEventListener('click', function () {
        const reservationId = this.dataset.reservationId;
        fetch(`/admin/reservations/${reservationId}/details`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('#detail-booking-id').textContent = data.reservation_reference || '—';
                document.querySelector('#detail-customer').textContent = `${data.customer_name} (${data.customer_email})`;
                document.querySelector('#detail-movie-cinema').textContent = `${data.movie_title} / ${data.cinema_name} / Screen ${data.screen_number}`;
                document.querySelector('#detail-showtime').textContent = data.showtime || '—';
                document.querySelector('#detail-seats').textContent = (data.seats && data.seats.length) ? data.seats.join(', ') : '—';
                document.querySelector('#detail-amount').textContent = `$${data.subtotal} / -$${data.discount_amount} / $${data.final_amount}`;
                document.querySelector('#detail-payment').textContent = `${data.payment_status} (${data.payment_method}) - ${data.transaction_id}`;
                document.querySelector('#detail-status').textContent = data.reservation_status;
                document.querySelector('#detail-qr').textContent = data.qr_code || '—';
            });
    });
});
</script>
@endsection