@extends('layouts.admin')

@section('title', 'Ticket Verification')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fa-solid fa-qrcode me-2"></i>
                    Ticket Verification
                </h2>

                <p class="text-muted mb-0">
                    Scan or enter a QR token to verify admission eligibility.
                </p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Verification Form --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form
                    action="{{ route('admin.tickets.verify') }}"
                    method="POST"
                >
                    @csrf

                    <div class="mb-3">
                        <label for="qr_token" class="form-label">
                            QR Token
                        </label>

                        <input
                            type="text"
                            id="qr_token"
                            name="qr_token"
                            class="form-control @error('qr_token') is-invalid @enderror"
                            value="{{ old('qr_token') }}"
                            placeholder="Scan or paste QR token"
                            autocomplete="off"
                            required
                            autofocus
                        >

                        @error('qr_token')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="btn btn-warning"
                    >
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Verify Ticket
                    </button>
                </form>
            </div>
        </div>

        {{-- Verification Result --}}
        @isset($status)
            @php
                $statusClasses = [
                    'valid' => 'success',
                    'used' => 'secondary',
                    'cancelled' => 'danger',
                    'expired' => 'warning',
                    'unpaid' => 'warning',
                    'invalid' => 'danger',
                ];

                $badgeClass = $statusClasses[$status] ?? 'secondary';

                $statusLabels = [
                    'valid' => 'VALID',
                    'used' => 'ALREADY USED',
                    'cancelled' => 'CANCELLED',
                    'expired' => 'EXPIRED',
                    'unpaid' => 'PAYMENT NOT COMPLETED',
                    'invalid' => 'INVALID',
                ];

                $statusLabel = $statusLabels[$status] ?? strtoupper($status);
            @endphp

            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="badge bg-{{ $badgeClass }} fs-5 px-3 py-2">
                            {{ $statusLabel }}
                        </span>

                        <span>
                            {{ $message ?? '' }}
                        </span>
                    </div>

                    @if ($ticket)
                        @php
                            $reservationSeat = $ticket->reservationSeat;
                            $reservation = $reservationSeat?->reservation;
                            $screenSeat = $reservationSeat?->showtimeSeat?->screenSeat;

                            $seatNumber = $screenSeat?->seat_number
                                ?? (
                                    ($screenSeat?->seat_row ?? '')
                                    . ($screenSeat?->seat_position ?? '')
                                );
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Movie</div>
                                    <div class="fw-semibold">
                                        {{ $reservation?->movie?->title ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Cinema</div>
                                    <div class="fw-semibold">
                                        {{ $reservation?->cinema?->cinema_name ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Seat</div>
                                    <div class="fw-semibold">
                                        {{ $seatNumber ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Screen</div>
                                    <div class="fw-semibold">
                                        {{ $reservation?->screen?->screen_number ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Showtime</div>
                                    <div class="fw-semibold">
                                        {{ $reservation?->showtime?->start_time?->format('M d, Y h:i A') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($ticket->used_at)
                            <div class="alert alert-secondary">
                                Used at:
                                <strong>
                                    {{ $ticket->used_at->format('M d, Y h:i A') }}
                                </strong>
                            </div>
                        @endif

                        @if ($status === 'valid')
                            <form
                                action="{{ route('admin.tickets.admit', $ticket) }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-success btn-lg"
                                >
                                    <i class="fa-solid fa-door-open me-1"></i>
                                    Admit Customer
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        @endisset
    </div>
@endsection