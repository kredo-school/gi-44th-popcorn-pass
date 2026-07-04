{{-- resources/views/emails/review-request.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Write a Review</title>
    <style>
        /*
         * Note: email clients (Gmail, Outlook, etc.) commonly strip <link>
         * stylesheets and many block external CSS entirely, so styles for
         * emails are kept self-contained in this <style> block rather than
         * in a separate imported .css file.
         */
        body {
            margin: 0;
            padding: 0;
            background-color: #1a1f36;
            font-family: Arial, sans-serif;
        }

        .email-wrapper {
            background-color: #1a1f36;
            padding: 40px 0;
            width: 100%;
        }

        .email-card {
            background-color: #232a47;
            border-radius: 12px;
            overflow: hidden;
            width: 480px;
        }

        .email-header {
            background-color: #000000;
            padding: 20px;
            text-align: center;
        }

        .email-header-logo {
            color: #ffd700;
            font-size: 20px;
            font-weight: bold;
        }

        .email-body {
            padding: 32px;
            color: #ffffff;
        }

        .email-greeting {
            margin-top: 0;
            color: #ffffff;
        }

        .email-text {
            color: #cfd2e3;
            font-size: 15px;
            line-height: 1.6;
        }

        .email-cta-wrap {
            text-align: center;
            margin: 32px 0;
        }

        .email-cta-button {
            background-color: #ffd700;
            color: #1a1f36;
            text-decoration: none;
            font-weight: bold;
            padding: 14px 32px;
            border-radius: 8px;
            display: inline-block;
        }

        .email-fallback-text {
            color: #9ca3c4;
            font-size: 13px;
        }

        .email-fallback-link {
            color: #ffd700;
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="email-wrapper">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" class="email-card">
                    <tr>
                        <td class="email-header">
                            <span class="email-header-logo">🍿 Popcorn Pass</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <h2 class="email-greeting">Hi {{ $user->first_name }},</h2>
                            <p class="email-text">
                                Thanks for watching <strong>{{ $movie->title }}</strong>
                                @if ($watchedDate)
                                    on {{ $watchedDate }}
                                @endif
                                ! We'd love to hear what you thought.
                            </p>
                            <div class="email-cta-wrap">
                                <a href="{{ $reviewUrl }}" class="email-cta-button">
                                    Write Your Review
                                </a>
                            </div>
                            <p class="email-fallback-text">
                                If the button doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ $reviewUrl }}" class="email-fallback-link">{{ $reviewUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>