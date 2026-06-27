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

    private function commonData()
    {
        $dates = collect();

        for ($i = 0; $i < 14; $i++) {
            $dates->push(now()->copy()->addDays($i));
        }

        return [
            'movies' => Movie::with('showtimes')->where('status', 'now_playing')->get(),
            'dates' => $dates,
        ];
    }

    public function showtime_display()
    {
        $data = $this->commonData();

        $data['selectedDate'] = request('date', now()->toDateString());
        $data['isSearch'] = false;

        return view('layouts.showtime_display', $data);
    }

    public function search(Request $request)
    {
        $data = $this->commonData();

        $keyword = $request->keyword;

        $data['searchResults'] = Movie::where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhere('director', 'like', "%{$keyword}%")
                ->orWhere('synopsis', 'like', "%{$keyword}%")
                ->orWhereHas('genre', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                })
                ->orWhereJsonContains('search_keywords', $keyword);
        })->get();

        $data['selectedDate'] = request('date', now()->toDateString());
        $data['isSearch'] = true;

        return view('layouts.showtime_display', $data);
    }
}
