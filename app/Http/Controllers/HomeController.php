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
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $movies = Movie::all();
        $comingSoonMovies = Movie::where('status', 'coming_soon')
                            ->orderBy('released_date', 'asc')
                            ->get();
        $topMovies = Movie::where('status', 'now_playing')
                    ->where('released_date', '>=', Carbon::now()->subWeek())
                    ->orderBy('popularity_score', 'desc')
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

    return view('layouts.showtime_display')->with('movies', $movies);
}
   
}
