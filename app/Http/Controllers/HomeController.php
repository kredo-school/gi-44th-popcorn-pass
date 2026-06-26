<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $movies = Movie::where('status', 'now_playing')
            ->get();
        $comingSoonMovies = Movie::where('status', 'coming_soon')
            ->whereDate('released_date', '>=', now())
            ->orderBy('released_date', 'asc')
            ->get();
        $topMovies = Movie::where('status', 'now_playing')
            ->orderBy('review_average', 'desc')
            ->take(3)
            ->get();
        return view('home')->with('movies', $movies)
            ->with('comingSoonMovies', $comingSoonMovies)
            ->with('topMovies', $topMovies);
    }

    public function showtime_display()
    {
        $movies = Movie::with('showtimes')
            ->where('status', 'now_playing')
            ->get();

        $dates = collect();
        for ($i = 0; $i < 14; $i++) {
            $dates->push(now()->copy()->addDays($i));
        }

        return view('layouts.showtime_display')->with('movies', $movies)
                                               ->with('dates' , $dates);
    }
}
