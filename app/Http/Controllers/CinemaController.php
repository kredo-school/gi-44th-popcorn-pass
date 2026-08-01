<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\View\View;

class CinemaController extends Controller
{
    /**
     * Display the specified cinema profile
     */
    public function show(Cinema $cinema): View
    {
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
}