<?php
// app/Services/LocationService.php
namespace App\Services;
use App\Models\Cinema;
class LocationService
{
    public function __construct(private GooglePlacesService $googlePlaces)
    {
    }

    /**
     * Resolve the cinema list to show on the home page.
     */
    public function resolveCinemas(?float $lat, ?float $lng): array
    {
        if ($lat !== null && $lng !== null && $this->googlePlaces->isConfigured()) {
            $nearby = $this->googlePlaces->nearbyCinemas($lat, $lng);
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

    public function fallbackCinemas(): array
    {
        return Cinema::where('is_active', true)
            ->orderBy('cinema_name')
            ->get()
            ->map(fn (Cinema $cinema) => [
                'id' => $cinema->id,
                'source' => 'database',
                'place_id' => null,
                'name' => $cinema->cinema_name,
                'address' => trim($cinema->address . ', ' . $cinema->city),
                'rating' => null,
                'is_open_now' => null,
                'distance_km' => null,
                'maps_url' => $cinema->website_url,
            ])
            ->all();
    }
}