<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\AgeRating;
use App\Models\Cinema;
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

    /**
     * List of valid reservation_status values used for the filter dropdown.
     * Defined here since there is no separate status master table.
     */
    private function reservationStatusOptions(): array
    {
        return ['pending', 'confirmed', 'cancelled', 'expired'];
    }

    /**
     * Builds the base reservations query with search/status/cinema filters applied.
     * Shared between the index page and the CSV export so both stay in sync.
     */
    private function buildReservationsQuery(Request $request)
    {
        $query = Reservation::with([
            'user',
            'movie',
            'cinema',
            'screen',
            'payment',
            'reservationSeats.showtimeSeat.screenSeat',
        ]);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('reservation_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('reservation_status', $request->get('status'));
        }

        if ($request->filled('cinema_id') && $request->get('cinema_id') !== 'all') {
            $query->where('cinema_id', $request->get('cinema_id'));
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function reservations(Request $request)
    {
        $reservations = $this->buildReservationsQuery($request)
            ->paginate(15)
            ->withQueryString();

        $cinemas = Cinema::orderBy('cinema_name')->get();
        $statusOptions = $this->reservationStatusOptions();

        return view('admin.reservations.index', compact('reservations', 'cinemas', 'statusOptions'));
    }

    public function reservationDetails($id)
    {
        $reservation = Reservation::with([
            'user',
            'movie',
            'cinema',
            'screen',
            'showtime',
            'payment',
            'reservationSeats.showtimeSeat.screenSeat',
        ])->findOrFail($id);

        return response()->json([
            'reservation_reference' => $reservation->reservation_reference,
            'customer_name' => $reservation->user->username ?? '—',
            'customer_email' => $reservation->user->email ?? '—',
            'movie_title' => $reservation->movie->title ?? '—',
            'cinema_name' => $reservation->cinema->cinema_name ?? '—',
            'screen_number' => $reservation->screen->screen_number ?? '—',
            'showtime' => optional($reservation->showtime?->start_time)->format('Y-m-d H:i'),
            'seats' => $reservation->seat_numbers,
            'total_seats' => $reservation->total_seats,
            'subtotal' => number_format($reservation->subtotal, 2),
            'discount_amount' => number_format($reservation->discount_amount, 2),
            'final_amount' => number_format($reservation->final_amount, 2),
            'reservation_status' => $reservation->reservation_status,
            'qr_code' => $reservation->qr_code,
            'confirmed_at' => optional($reservation->confirmed_at)->format('Y-m-d H:i'),
            'cancelled_at' => optional($reservation->cancelled_at)->format('Y-m-d H:i'),
            'payment_status' => $reservation->payment->payment_status ?? '—',
            'payment_method' => $reservation->payment->payment_method ?? '—',
            'transaction_id' => $reservation->payment->transaction_id ?? '—',
            'paid_at' => optional($reservation->payment?->paid_at)->format('Y-m-d H:i'),
        ]);
    }

    public function exportReservationsCsv(Request $request)
    {
        $reservations = $this->buildReservationsQuery($request)->get();

        $filename = 'reservations_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Booking ID', 'Customer', 'Movie', 'Cinema', 'Screen', 'Seats', 'Amount', 'Payment', 'Status']);

            foreach ($reservations as $reservation) {
                fputcsv($file, [
                    $reservation->reservation_reference,
                    $reservation->user->username ?? '—',
                    $reservation->movie->title ?? '—',
                    $reservation->cinema->cinema_name ?? '—',
                    $reservation->screen->screen_number ?? '—',
                    $reservation->seat_numbers->implode(','),
                    number_format($reservation->final_amount, 2),
                    $reservation->payment->payment_status ?? 'unpaid',
                    $reservation->reservation_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}