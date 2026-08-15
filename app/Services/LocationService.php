<?php

namespace App\Services;

use App\Models\Cinema;

class LocationService
{
    public function __construct(
        private GooglePlacesService $googlePlaces
    ) {
    }

    /**
     * Resolve the cinema list to show on the home page.
     *
     * Priority:
     * 1. Google Places when location is available and configured
     * 2. Database cinemas as fallback
     */
    public function resolveCinemas(?float $lat, ?float $lng): array
    {
        if (
            $lat !== null &&
            $lng !== null &&
            $this->googlePlaces->isConfigured()
        ) {
            $nearby = $this->googlePlaces->nearbyCinemas(
                $lat,
                $lng
            );

            if (!empty($nearby)) {
                return [
                    'source' => 'google_places',
                    'cinemas' => $nearby,
                ];
            }
        }

        return [
            'source' => 'database',
            'cinemas' => $this->fallbackCinemas(),
        ];
    }

    /**
     * Get active cinemas from the Popcorn Pass database.
     *
     * These values are used by:
     * - Nearby cinema cards
     * - Home Mini Map
     * - Cinema selection
     */
    public function fallbackCinemas(): array
    {
        return Cinema::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('cinema_name')
            ->get()
            ->map(function (Cinema $cinema) {
                return [
                    /**
                     * Cinema identity
                     */
                    'id' => $cinema->id,
                    'source' => 'database',
                    'place_id' => null,

                    /**
                     * Cinema information
                     */
                    'name' => $cinema->cinema_name,
                    'city' => $cinema->city,
                    'address' => trim(
                        $cinema->address . ', ' . $cinema->city
                    ),

                    /**
                     * Coordinates
                     *
                     * Required by the Home Mini Map.
                     */
                    'latitude' => $cinema->latitude !== null
                        ? (float) $cinema->latitude
                        : null,

                    'longitude' => $cinema->longitude !== null
                        ? (float) $cinema->longitude
                        : null,

                    /**
                     * Optional information
                     */
                    'rating' => null,
                    'is_open_now' => null,
                    'distance_km' => null,

                    /**
                     * Official cinema website
                     */
                    'website_url' => $cinema->website_url,

                    /**
                     * Popcorn Pass cinema home.
                     *
                     * Used when the user selects a cinema.
                     */
                    'home_url' => route(
                        'cinemas.home',
                        ['cinema' => $cinema->id]
                    ),

                    /**
                     * Existing Popcorn Pass visit route.
                     */
                    'visit_url' => route(
                        'cinemas.visit',
                        $cinema
                    ),
                ];
            })
            ->all();
    }
}