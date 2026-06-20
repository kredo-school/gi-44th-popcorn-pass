<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\AgeRating;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function movies()
    {
        $movies = Movie::with(['genre', 'ageRating'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.movies.index', compact('movies'));
    }

    public function movieDetails($id)
    {
        $movie = Movie::findOrFail($id);

        return response()->json([
            'title' => $movie->title,
            'synopsis' => $movie->synopsis,
            'director' => $movie->director,
            'cast' => $movie->cast,
            'trailer_url' => $movie->trailer_url,
        ]);
    }

    public function createMovie()
    {
        $genres = Genre::orderBy('title')->get();
        $ageRatings = AgeRating::orderBy('title')->get();

        return view('admin.movies.create', compact('genres', 'ageRatings'));
    }

    public function storeMovie(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'duration' => 'required|integer|min:1',
            'age_rating_id' => 'nullable|exists:age_ratings,id',
            'released_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'synopsis' => 'nullable|string',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|string',
            'trailer_url' => 'nullable|url',
            'budget' => 'nullable|numeric',
            'box_office' => 'nullable|numeric',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['cast'] = $validated['cast'] ?? null;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['created_by_id'] = auth()->id();

        Movie::create($validated);

        return redirect()->route('admin.movies')->with('success', 'Movie added successfully.');
    }

    public function analytics()
    {
        $dailyRevenue = Payment::where('payment_status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('amount');

        $weeklyRevenue = Payment::where('payment_status', 'paid')
            ->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $monthlyRevenue = Payment::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $avgTicketPrice = Payment::where('payment_status', 'paid')->avg('amount') ?? 0;

        $dailyRevenueChart = Payment::where('payment_status', 'paid')
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(7)
            ->get();

        $topMovies = Reservation::selectRaw('movie_id, SUM(final_amount) as total_revenue, COUNT(*) as ticket_count')
            ->groupBy('movie_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('movie')
            ->get();

        return view('admin.analytics.index', compact(
            'dailyRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'avgTicketPrice',
            'dailyRevenueChart',
            'topMovies'
        ));
    }

    public function reservations()
    {
        $reservations = Reservation::with(['user', 'movie', 'cinema', 'screen', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }
}