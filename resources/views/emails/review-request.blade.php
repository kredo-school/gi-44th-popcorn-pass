{{-- resources/views/emails/review-request.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Write a Review</title>
</head>
<body style="margin:0; padding:0; background-color:#1a1f36; font-family: Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#1a1f36; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0"
                       style="background-color:#232a47; border-radius: 12px; overflow: hidden;">
                    <tr>
                        <td style="background-color:#000000; padding: 20px; text-align:center;">
                            <span style="color:#FFD700; font-size: 20px; font-weight:bold;">🍿 Popcorn Pass</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px; color:#ffffff;">
                            <h2 style="margin-top:0; color:#ffffff;">Hi {{ $user->first_name }},</h2>
                            <p style="color:#cfd2e3; font-size: 15px; line-height: 1.6;">
                                Thanks for watching <strong>{{ $movie->title }}</strong>
                                @if ($watchedDate)
                                    on {{ $watchedDate }}
                                @endif
                                ! We'd love to hear what you thought.
                            </p>
                            <div style="text-align:center; margin: 32px 0;">
                                <a href="{{ $reviewUrl }}"
                                   style="background-color:#FFD700; color:#1a1f36; text-decoration:none; font-weight:bold; padding: 14px 32px; border-radius: 8px; display:inline-block;">
                                    Write Your Review
                                </a>
                            </div>
                            <p style="color:#9ca3c4; font-size: 13px;">
                                If the button doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ $reviewUrl }}" style="color:#FFD700;">{{ $reviewUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>