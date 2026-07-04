<?php
// app/Services/GooglePlacesService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_places.key');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Find nearby movie theaters using the Google Places Nearby Search API.
     * Returns a normalized array of cinemas, sorted by distance, capped at $limit.
     * Results are cached for 1 hour per rounded coordinate to save API quota.
     */
    public function nearbyCinemas(float $lat, float $lng, int $radiusMeters = 5000, int $limit = 5): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $cacheKey = sprintf('nearby-cinemas:%s:%s:%s', round($lat, 3), round($lng, 3), $radiusMeters);

        return Cache::remember($cacheKey, now()->addHour(), function () use ($lat, $lng, $radiusMeters, $limit) {
            try {
                $response = Http::timeout(6)->get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                    'location' => "{$lat},{$lng}",
                    'radius' => $radiusMeters,
                    'type' => 'movie_theater',
                    'key' => $this->apiKey,
                ]);

                if (!$response->successful()) {
                    Log::warning('Google Places nearby search HTTP error', ['status' => $response->status()]);
                    return [];
                }

                $body = $response->json();

                if (($body['status'] ?? null) !== 'OK') {
                    Log::warning('Google Places nearby search non-OK status', ['status' => $body['status'] ?? null]);
                    return [];
                }

                return collect($body['results'] ?? [])
                    ->map(function (array $place) use ($lat, $lng) {
                        $placeLat = $place['geometry']['location']['lat'] ?? null;
                        $placeLng = $place['geometry']['location']['lng'] ?? null;

                        return [
                            'source' => 'google_places',
                            'place_id' => $place['place_id'] ?? null,
                            'name' => $place['name'] ?? 'Unknown Cinema',
                            'address' => $place['vicinity'] ?? null,
                            'rating' => $place['rating'] ?? null,
                            'is_open_now' => $place['opening_hours']['open_now'] ?? null,
                            'distance_km' => ($placeLat && $placeLng)
                                ? round($this->haversineKm($lat, $lng, $placeLat, $placeLng), 1)
                                : null,
                            'maps_url' => isset($place['place_id'])
                                ? "https://www.google.com/maps/place/?q=place_id:{$place['place_id']}"
                                : null,
                        ];
                    })
                    ->sortBy('distance_km')
                    ->values()
                    ->take($limit)
                    ->all();
            } catch (\Throwable $e) {
                Log::error('Google Places nearby search exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}