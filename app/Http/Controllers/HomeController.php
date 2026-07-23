<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Information;
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
        $movies = Movie::where('status', 'now_showing')
            ->get();
        $comingSoonMovies = Movie::where('status', 'coming_soon')
            ->whereDate('released_date', '>=', now())
            ->orderBy('released_date', 'asc')
            ->get();
        $topMovies = Movie::withAvg('reviews', 'rating')
            ->where('status', 'now_showing')
            ->orderByDesc('review_average')
            ->take(10)
            ->get();

        $heroMovie = Movie::where('status', 'coming_soon')
            ->inRandomOrder()
            ->first();
        $topMovie = Movie::withAvg([
            'reviews as weekly_average' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subWeek());
            }
        ], 'rating')
            ->orderByDesc('weekly_average')
            ->first();
        $information = Information::where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('home')->with('movies', $movies)
            ->with('comingSoonMovies', $comingSoonMovies)
            ->with('topMovies', $topMovies)
            ->with('heroMovie', $heroMovie)
            ->with('topMovie', $topMovie)
            ->with('information', $information);
    }

    private function commonData($selectedDate)
    {
        $dates = collect();

        for ($i = 0; $i < 14; $i++) {
            $dates->push(now()->copy()->addDays($i));
        }

        return [
            'movies' => Movie::with([
                'showtimes' => function ($query) use ($selectedDate) {
                    $query->whereDate('start_time', $selectedDate);
                },
                'showtimes.screen.cinema'
            ])
                ->where('status', 'now_showing')
                ->get(),

            'comingSoonMovies' => Movie::where('status', 'coming_soon')
                ->whereDate('released_date', '>=', now())
                ->orderBy('released_date')
                ->get(),

            'heroMovie' => Movie::where('status', 'coming_soon')
                ->inRandomOrder()
                ->first(),

            'topMovie' => Movie::withAvg([
                'reviews as weekly_average' => function ($query) {
                    $query->where('created_at', '>=', now()->subWeek());
                }
            ], 'rating')
                ->orderByDesc('weekly_average')
                ->first(),

            'information' => Information::where('status', 'Published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->take(5)
                ->get(),

            'dates' => $dates,
            'selectedDate' => $selectedDate,
        ];
    }

    public function showtime_display()
    {
        $selectedDate = request('date', today()->format('Y-m-d'));

        $data = $this->commonData($selectedDate);

        $data['selectedDate'] = $selectedDate;
        $data['isSearch'] = false;

        $heroMovie = Movie::where('status', 'coming_soon')
            ->inRandomOrder()
            ->first();
        $topMovie = Movie::withAvg([
            'reviews as weekly_average' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subWeek());
            }
        ], 'rating')
            ->orderByDesc('weekly_average')
            ->first();

        return view('layouts.showtime_display', $data)
            ->with('heroMovie', $heroMovie)
            ->with('topMovie', $topMovie);
    }

    public function home_search(Request $request)
    {
        $selectedDate = request('date', now()->toDateString());

        $data = $this->commonData($selectedDate);

        $keyword = $request->keyword;

        $data['searchResults'] = Movie::with(['showtimes' => function ($query) use ($selectedDate) {
            $query->whereDate('start_time', $selectedDate);
        }, 'showtimes.screen.cinema'])
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('director', 'like', "%{$keyword}%")
                    ->orWhere('synopsis', 'like', "%{$keyword}%")
                    ->orWhereHas('genre', function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    })
                    ->orWhereJsonContains('search_keywords', $keyword);
            })->get();

        $data['selectedDate'] = $selectedDate;
        $data['isSearch'] = true;

        return view('layouts.showtime_display', $data);
    }

    public function showtime_search(Request $request)
    {
        $selectedDate = $request->get('date', now()->toDateString());
        $keyword = $request->keyword;

        $data = $this->commonData($selectedDate);

        $data['searchResults'] = Movie::with([
            'showtimes' => function ($query) use ($selectedDate) {
                $query->whereDate('start_time', $selectedDate);
            },
            'showtimes.screen.cinema',
            'genre',
        ])
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('director', 'like', "%{$keyword}%")
                    ->orWhere('synopsis', 'like', "%{$keyword}%")
                    ->orWhereHas('genre', function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    });
            })
            ->get();

        $data['selectedDate'] = $selectedDate;
        $data['isSearch'] = true;

        return view('layouts.showtime_display', $data);
    }

    // showtime selection
    public function showtime_selection(Movie $movie)
    {
        $selectedDate = request('date', today()->format('Y-m-d'));

        $data = $this->commonData($selectedDate);

        $movie->load([
            'showtimes' => function ($query) use ($selectedDate) {
                $query->whereDate('start_time', $selectedDate);
            },
            'showtimes.screen.cinema',
        ]);


        $data['movie'] = $movie;
        $data['selectedDate'] = $selectedDate;
        $data['isSearch'] = false;

        return view('reservations.showtime-selection', $data);
    }

    // Relese display
    public function release(Movie $movie)
    {
        return view('movies.release')->with('movie', $movie);
    }

    // movie detail
    public function movie_detail(Movie $movie)
    {
        return view('movies.movie_detail')->with('movie', $movie);
    }

    // search movie 
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $movies = Movie::where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhere('director', 'like', "%{$keyword}%")
                ->orWhere('synopsis', 'like', "%{$keyword}%")
                ->orWhereHas('genre', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                })
                ->orWhereJsonContains('search_keywords', $keyword);
        })->get();

        return view('movies.search', compact(
            'movies',
            'keyword'
        ));
    }

    //information index
    public function informationIndex()
    {
        $information = Information::where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(10);

        return view('information.index', compact('information'));
    }

    //information detail
    public function informationDetail($id)
    {
        $information = Information::with('category')
            ->where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->findOrFail($id);
        return view('information.information-detail', compact('information'));
    }
}
