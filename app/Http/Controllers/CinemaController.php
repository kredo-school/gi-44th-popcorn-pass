<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CinemaController extends Controller
{
    /**
     * Display the specified cinema profile.
     */
    public function show(Cinema $cinema): View
    {
        // Save the currently selected cinema in the session.
        // This will later be used to display:
        // POPCORN PASS - Osaka / Tokyo / Nagoya
        session([
            'selected_cinema_id' => $cinema->id,
        ]);

        $cinema->load('reviews.user');

        // Refactored score structure for the loop
        $scoreBreakdown = [
            [
                'icon' => 'fa-film',
                'label' => 'Image Quality',
                'key' => 'image_quality',
                'inverted' => false,
                'value' => $cinema->avg_image_quality,
            ],
            [
                'icon' => 'fa-volume-high',
                'label' => 'Sound Quality',
                'key' => 'sound_quality',
                'inverted' => false,
                'value' => $cinema->avg_sound_quality,
            ],
            [
                'icon' => 'fa-chair',
                'label' => 'Seat Comfort',
                'key' => 'seat_comfort',
                'inverted' => false,
                'value' => $cinema->avg_seat_comfort,
            ],
            [
                'icon' => 'fa-people-group',
                'label' => 'Crowding Level',
                'key' => 'crowding_level',
                'inverted' => true,
                'value' => $cinema->avg_crowding_level,
                'note' => '(lower is better)',
            ],
            [
                'icon' => 'fa-wheelchair',
                'label' => 'Accessibility',
                'key' => 'accessibility',
                'inverted' => false,
                'value' => $cinema->avg_accessibility,
            ],
            [
                'icon' => 'fa-handshake',
                'label' => 'Service Quality',
                'key' => 'service_quality',
                'inverted' => false,
                'value' => $cinema->avg_service_quality,
            ],
        ];

        $reviews = $cinema->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('cinemas.show', [
            'cinema' => $cinema,
            'scoreBreakdown' => $scoreBreakdown,
            'reviews' => $reviews,
        ]);
    }

    /**
     * Save the selected cinema and redirect
     * the user to the cinema's official website.
     */
    public function visit(Cinema $cinema): RedirectResponse
    {
        // Remember which cinema the user selected.
        session([
            'selected_cinema_id' => $cinema->id,
        ]);

        /**
         * If an official website has not been registered,
         * redirect to the existing cinema profile page
         * instead of returning a 404/500 error.
         */
        if (blank($cinema->website_url)) {
            return redirect()
                ->route('cinemas.show', $cinema)
                ->with(
                    'warning',
                    'The official cinema website is not available.'
                );
        }

        /**
         * Security check:
         * Only allow normal HTTP / HTTPS URLs.
         */
        $scheme = parse_url($cinema->website_url, PHP_URL_SCHEME);

        if (!in_array($scheme, ['http', 'https'], true)) {
            return redirect()
                ->route('cinemas.show', $cinema)
                ->with(
                    'warning',
                    'The official cinema website URL is invalid.'
                );
        }

        // Redirect outside Popcorn Pass to the official cinema website.
        return redirect()->away($cinema->website_url);
    }
}