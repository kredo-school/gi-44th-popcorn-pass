<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>

    <style>
        {!! file_get_contents(resource_path('css/emails/purchase-confirmation.css')) !!}
    </style>
</head>

<body>
    @php
        $customerName = $reservation->user
            ? $reservation->user->first_name
            : ($reservation->guest_first_name ?? 'Guest');

        $movieTitle = $reservation->movie?->title ?? 'Movie';
        $cinemaName = $reservation->cinema?->cinema_name ?? 'Cinema';
        $showtime = $reservation->showtime?->start_time;
        $seatNumbers = $reservation->seat_numbers;
        $paymentMethod = $reservation->payment?->payment_method;

        $paymentMethodLabel = match ($paymentMethod) {
            'paypal' => 'PayPal',
            'onsite' => 'Pay at Cinema',
            default => $paymentMethod
                ? ucfirst($paymentMethod)
                : 'N/A',
        };
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="email-wrapper">
        <tr>
            <td align="center">

                <table role="presentation" width="560" cellpadding="0" cellspacing="0" class="email-card">
                    <tr>
                        <td class="email-header">
                            <span class="email-logo">POPCORN PASS</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-body">

                            <h1 class="email-title">
                                Your booking is confirmed!
                            </h1>

                            <p class="email-text">
                                Hi {{ $customerName }},
                            </p>

                            <p class="email-text">
                                Thank you for booking with Popcorn Pass.
                                Your reservation has been successfully confirmed.
                            </p>

                            <div class="reference-box">
                                <div class="reference-label">
                                    Reservation Reference
                                </div>

                                <div class="reference-value">
                                    {{ $reservation->reservation_reference }}
                                </div>
                            </div>

                            <h2 class="section-title">
                                Booking Details
                            </h2>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="summary-table">
                                <tr>
                                    <td class="summary-label">Movie</td>
                                    <td class="summary-value">{{ $movieTitle }}</td>
                                </tr>

                                <tr>
                                    <td class="summary-label">Cinema</td>
                                    <td class="summary-value">{{ $cinemaName }}</td>
                                </tr>

                                @if ($reservation->screen)
                                    <tr>
                                        <td class="summary-label">Screen</td>
                                        <td class="summary-value">
                                            {{ $reservation->screen->screen_name ?? $reservation->screen->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="summary-label">Showtime</td>
                                    <td class="summary-value">
                                        {{ $showtime ? $showtime->format('M d, Y - h:i A') : 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="summary-label">Seats</td>
                                    <td class="summary-value">
                                        {{ $seatNumbers->isNotEmpty() ? $seatNumbers->join(', ') : 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="summary-label">Number of Tickets</td>
                                    <td class="summary-value">
                                        {{ $reservation->total_seats }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="summary-label">Payment Method</td>
                                    <td class="summary-value">
                                        {{ $paymentMethodLabel }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="summary-label">Subtotal</td>
                                    <td class="summary-value">
                                        ${{ number_format((float) $reservation->subtotal, 2) }}
                                    </td>
                                </tr>

                                @if ((float) $reservation->discount_amount > 0)
                                    <tr>
                                        <td class="summary-label">Discount</td>
                                        <td class="summary-value">
                                            -${{ number_format((float) $reservation->discount_amount, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                <tr class="total-row">
                                    <td class="total-label">Total</td>
                                    <td class="total-value">
                                        ${{ number_format((float) $reservation->final_amount, 2) }}
                                    </td>
                                </tr>
                            </table>

                            @if ($ticketUrl)
                                <div class="email-cta-wrap">
                                    <a href="{{ $ticketUrl }}" class="email-cta-button">
                                        View Your E-Ticket
                                    </a>
                                </div>

                                <p class="email-note">
                                    If the button does not work, copy and paste
                                    the following link into your browser:<br>

                                    <a href="{{ $ticketUrl }}" class="email-fallback-link">
                                        {{ $ticketUrl }}
                                    </a>
                                </p>
                            @else
                                <p class="email-note">
                                    Please keep your reservation reference safe.
                                    You may need it when contacting the cinema
                                    about your booking.
                                </p>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer">
                            Thank you for choosing Popcorn Pass.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
