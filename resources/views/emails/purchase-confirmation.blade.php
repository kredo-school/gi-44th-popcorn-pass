<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>

    <style>
        /*
         * Email clients commonly block external stylesheets.
         * Keep all email styles self-contained in this <style> block.
         */
        body {
            margin: 0;
            padding: 0;
            background-color: #1a1f36;
            font-family: Arial, Helvetica, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            padding: 40px 0;
            background-color: #1a1f36;
        }

        .email-card {
            width: 560px;
            max-width: 560px;
            background-color: #232a47;
            border-radius: 12px;
            overflow: hidden;
        }

        .email-header {
            padding: 24px;
            background-color: #000000;
            text-align: center;
        }

        .email-logo {
            color: #ffd700;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .email-body {
            padding: 32px;
            color: #ffffff;
        }

        .email-title {
            margin: 0 0 12px;
            color: #ffffff;
            font-size: 24px;
        }

        .email-text {
            margin: 0 0 20px;
            color: #cfd2e3;
            font-size: 15px;
            line-height: 1.6;
        }

        .reference-box {
            margin: 24px 0;
            padding: 18px;
            background-color: #1a1f36;
            border: 1px solid #3a4268;
            border-radius: 8px;
            text-align: center;
        }

        .reference-label {
            margin-bottom: 8px;
            color: #9ca3c4;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reference-value {
            color: #ffd700;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .section-title {
            margin: 28px 0 12px;
            color: #ffd700;
            font-size: 16px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 10px 0;
            border-bottom: 1px solid #3a4268;
            vertical-align: top;
            font-size: 14px;
        }

        .summary-label {
            width: 38%;
            color: #9ca3c4;
        }

        .summary-value {
            color: #ffffff;
            text-align: right;
        }

        .total-row td {
            padding-top: 16px;
            border-bottom: 0;
            font-size: 17px;
            font-weight: bold;
        }

        .total-label {
            color: #ffffff;
        }

        .total-value {
            color: #ffd700;
            text-align: right;
        }

        .email-cta-wrap {
            margin: 32px 0;
            text-align: center;
        }

        .email-cta-button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #ffd700;
            border-radius: 8px;
            color: #1a1f36;
            font-weight: bold;
            text-decoration: none;
        }

        .email-note {
            margin-top: 24px;
            color: #9ca3c4;
            font-size: 13px;
            line-height: 1.6;
        }

        .email-fallback-link {
            color: #ffd700;
            word-break: break-all;
        }

        .email-footer {
            padding: 20px 32px;
            background-color: #1d233d;
            color: #7f87a8;
            font-size: 12px;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .email-card {
                width: 94%;
            }

            .email-body {
                padding: 24px;
            }
        }
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
