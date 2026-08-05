<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    /**
     * Display the map with cinemas
     */
    public function index()
    {
        // Get all cinemas from database
        $cinemas = DB::table('cinemas')
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('map.index', [
            'cinemas' => $cinemas,
            'googleMapsApiKey' => config('maps.google.api_key'),
        ]);
    }
}