<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\AgeRating;
use App\Models\Cinema;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\SystemSetting;
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

    private function movieValidationRules(): array
    {
        return [
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
            'search_keywords' => 'nullable|string',
            'trailer_url' => 'nullable|url',
            'poster_url' => 'nullable|url',
            'banner_image_url' => 'nullable|url',
            'budget' => 'nullable|numeric',
            'box_office' => 'nullable|numeric',
            'priority_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
        ];
    }

    public function storeMovie(Request $request)
    {
        $validated = $request->validate($this->movieValidationRules());

        $validated['cast'] = $validated['cast'] ?? null;
        $validated['search_keywords'] = $validated['search_keywords'] ?? null;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['created_by_id'] = auth()->id();

        Movie::create($validated);

        return redirect()->route('admin.movies')->with('success', 'Movie added successfully.');
    }

    public function editMovie($id)
    {
        $movie = Movie::findOrFail($id);
        $genres = Genre::orderBy('title')->get();
        $ageRatings = AgeRating::orderBy('title')->get();

        return view('admin.movies.edit', compact('movie', 'genres', 'ageRatings'));
    }

    public function updateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate($this->movieValidationRules());

        $validated['cast'] = $validated['cast'] ?? null;
        $validated['search_keywords'] = $validated['search_keywords'] ?? null;
        $validated['is_featured'] = $request->has('is_featured');

        $movie->update($validated);

        return redirect()->route('admin.movies')->with('success', 'Movie updated successfully.');
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

    private function reservationStatusOptions(): array
    {
        return ['pending', 'confirmed', 'cancelled', 'expired'];
    }

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

    private function userRoleOptions(): array
    {
        return [
            1 => 'Customer',
            2 => 'Admin',
            3 => 'Manager',
            4 => 'Support',
        ];
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('is_active', $request->get('status') === 'active' ? 1 : 0);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $roleOptions = $this->userRoleOptions();

        return view('admin.users.index', compact('users', 'roleOptions'));
    }

    public function userDetails($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'date_of_birth' => optional($user->date_of_birth)->format('Y-m-d'),
            'role' => (int) $user->role,
            'is_active' => (bool) $user->is_active,
            'last_login_at' => optional($user->last_login_at)->format('Y-m-d H:i'),
            'created_at' => optional($user->created_at)->format('Y-m-d H:i'),
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|integer|in:1,2,3,4',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function settings()
    {
        $settings = SystemSetting::current();

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = SystemSetting::current();

        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'timezone' => 'required|string|max:50',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|max:20',
            'notification_email' => 'nullable|email|max:255',
            'payment_gateway' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stripe_publishable_key' => 'nullable|string|max:255',
        ]);

        $settings->update($validated);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
}