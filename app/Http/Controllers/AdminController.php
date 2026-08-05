<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\AgeRating;
use App\Models\Cinema;
use App\Models\Screen;
use App\Models\ScreenSeat;
use App\Models\Showtime;
use App\Models\ShowtimeSeat;
use App\Models\ReservationSeat;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\Information;
use App\Models\InformationCategory;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatRequest;


use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    // --------------------
    // Dashboard
    // --------------------
    public function dashboard()
    {
        $thisYear = now()->year;

        // ===== Revenue（this year）=====
        $thisYearRevenue = Payment::whereYear('created_at', now()->year)->sum('amount');
        $lastYearRevenue = Payment::whereYear('created_at', now()->subYear()->year)->sum('amount');

        $revenueChange = $lastYearRevenue > 0
            ? (($thisYearRevenue - $lastYearRevenue) / $lastYearRevenue) * 100
            : 0;


        // ===== Users（今年登録したユーザー）=====
        $totalUsers = User::whereYear('created_at', now()->year)->count();
        $lastYearUsers = User::whereYear('created_at', now()->subYear()->year)->count();

        $userChange = $lastYearUsers > 0
            ? (($totalUsers - $lastYearUsers) / $lastYearUsers) * 100
            : 0;


        // ===== Movies（今年登録した映画）=====
        $activeMovies = Movie::whereYear('created_at', now()->year)->count();
        $lastYearMovies = Movie::whereYear('created_at', now()->subYear()->year)->count();

        $movieChange = $lastYearMovies > 0
            ? (($activeMovies - $lastYearMovies) / $lastYearMovies) * 100
            : 0;


        // ===== Reservations（今年の予約件数）=====
        $totalReservations = Reservation::whereYear('created_at', now()->year)->count();
        $lastYearReservations = Reservation::whereYear('created_at', now()->subYear()->year)->count();

        $reservationChange = $lastYearReservations > 0
            ? (($totalReservations - $lastYearReservations) / $lastYearReservations) * 100
            : 0;


        //Revenue Trend
        $monthlyRevenue = Payment::selectRaw('
        MONTH(created_at) as month,
        SUM(amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();


        $revenueData = array_fill(0, 12, 0);

        foreach ($monthlyRevenue as $item) {
            $revenueData[$item->month - 1] = $item->total;
        }

        // top peforming movies

        $movieSalesRanking = Payment::join('reservations', 'payments.reservation_id', '=', 'reservations.id')
            ->join('movies', 'reservations.movie_id', '=', 'movies.id')
            ->select(
                'movies.title',
                DB::raw('SUM(payments.amount) as total_sales')
            )
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        //Recent Reservation
        $recentReservations = Reservation::with([
            'user',
            'movie',
            'screen',
            'showtime'
        ])
            ->latest('created_at')
            ->take(5)
            ->get();



        return view('admin.dashboard', compact(
            'thisYearRevenue',
            'revenueChange',
            'totalUsers',
            'userChange',
            'activeMovies',
            'movieChange',
            'totalReservations',
            'reservationChange',
            'thisYear',
            'movieSalesRanking',
            'recentReservations',
            'revenueData'
        ));
    }

    // --------------------
    // Movies
    // --------------------
    public function movies()
    {
        Movie::syncStatuses();

        $movies = Movie::with(['genres', 'ageRating'])
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
        $cinemas = Cinema::orderBy('cinema_name')->get();
        $screens = Screen::with('cinema')->orderBy('screen_number')->get();

        return view('admin.movies.create', compact('genres', 'ageRatings', 'cinemas', 'screens'));
    }

    private function movieValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'genre_ids' => 'required|array|min:1|max:3',
            'genre_ids.*' => 'required|exists:genres,id',
            'duration' => 'required|integer|min:1',
            'age_rating_id' => 'nullable|exists:age_ratings,id',
            'released_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:released_date',
            'status' => 'nullable',
            'synopsis' => 'nullable|string',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|array|max:6',
            'cast.*' => 'nullable|string|max:100',
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

    private function convertYoutubeUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        preg_match('/(?:youtu\.be\/|v=)([^?&]+)/', $url, $matches);

        if (!isset($matches[1])) {
            return $url;
        }

        $videoId = $matches[1];

        return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&playlist={$videoId}";
    }

    public function storeMovie(Request $request)
    {
        $rules = $this->movieValidationRules();

        $rules['showtime_generate'] = 'nullable|array';
        $rules['showtime_generate.cinema_id'] =
            'nullable|exists:cinemas,id';
        $rules['showtime_generate.screen_id'] =
            'nullable|exists:screens,id';
        $rules['showtime_generate.days'] =
            'nullable|array|min:1';
        $rules['showtime_generate.days.*'] =
            'integer|between:0,6';
        $rules['showtime_generate.slots'] =
            'nullable|array|max:6';
        $rules['showtime_generate.slots.*'] =
            'nullable|date_format:H:i';

        $validated = $request->validate($rules);

        $showtimeGenerate =
            $validated['showtime_generate'] ?? [];

        unset($validated['showtime_generate']);

        $genreIds = $validated['genre_ids'];
        unset($validated['genre_ids']);

        $validated['duration'] =
            (int) $validated['duration'];

        $validated['cast'] =
            !empty($validated['cast'])
            ? json_encode(array_values(array_filter(
                $validated['cast']
            )))
            : null;

        $validated['search_keywords'] =
            $validated['search_keywords'] ?? null;

        $validated['trailer_url'] =
            $this->convertYoutubeUrl(
                $validated['trailer_url'] ?? null
            );

        $validated['is_featured'] =
            $request->has('is_featured');

        $validated['created_by_id'] =
            auth()->id();

        $today = now()->toDateString();

        if ($validated['released_date'] > $today) {
            $validated['status'] = 'coming_soon';
        } elseif (!empty($validated['end_date']) && $validated['end_date'] < $today) {
            $validated['status'] = 'archived';
        } else {
            $validated['status'] = 'now_showing';
        }

        $movie = Movie::create($validated);

        $movie->genres()->sync($genreIds);

        $showtimeWarnings = [];

        if ($this->hasShowtimeGenerationInput($showtimeGenerate)) {
            $showtimeWarnings =
                $this->generateShowtimesFromMovieDates(
                    $movie,
                    $showtimeGenerate
                );
        }

        $message = 'Movie added successfully.';

        if (!empty($showtimeWarnings)) {
            $message .=
                ' However, some showtimes could not be created: '
                . implode(' ', $showtimeWarnings);
        }

        return redirect()
            ->route('admin.movies')
            ->with('success', $message);
    }

    private function hasShowtimeGenerationInput(
        array $showtimeGenerate
    ): bool {
        return !empty($showtimeGenerate['screen_id'])
            || !empty($showtimeGenerate['days'])
            || !empty(array_filter(
                $showtimeGenerate['slots'] ?? []
            ));
    }

    private function generateShowtimesFromMovieDates(
        Movie $movie,
        array $input
    ): array {
        $warnings = [];

        $screenId = $input['screen_id'] ?? null;
        $days = $input['days'] ?? [];
        $slots = array_values(array_filter(
            $input['slots'] ?? []
        ));

        if (!$screenId || empty($days) || empty($slots)) {
            return [
                'Screen, days, and at least one time slot are required.',
            ];
        }

        if (!$movie->released_date || !$movie->end_date) {
            return [
                'Movie release date and end date are required.',
            ];
        }

        $screen = Screen::find($screenId);

        if (!$screen) {
            return ['Screen not found.'];
        }

        $durationMinutes = (int) $movie->duration;

        $current = Carbon::parse(
            $movie->released_date
        )->startOfDay();

        $end = Carbon::parse(
            $movie->end_date
        )->startOfDay();

        while ($current->lte($end)) {
            $dayOfWeek = (int) $current->format('w');

            if (!in_array($dayOfWeek, $days)) {
                $current->addDay();
                continue;
            }

            foreach ($slots as $slot) {
                $startsAt = Carbon::parse(
                    $current->format('Y-m-d') . ' ' . $slot
                );

                $endsAt = $startsAt
                    ->copy()
                    ->addMinutes($durationMinutes);

                $overlap = Showtime::where(
                    'screen_id',
                    $screen->id
                )
                    ->where(function ($query) use (
                        $startsAt,
                        $endsAt
                    ) {
                        $query
                            ->where('start_time', '<', $endsAt)
                            ->where('end_time', '>', $startsAt);
                    })
                    ->exists();

                if ($overlap) {
                    $warnings[] =
                        $current->format('Y-m-d')
                        . " {$slot}: overlaps with another showtime.";

                    continue;
                }

                $showtime = Showtime::create([
                    'screen_id' => $screen->id,
                    'movie_id' => $movie->id,
                    'start_time' => $startsAt,
                    'end_time' => $endsAt,
                    'is_active' => true,
                    'created_by_id' => auth()->id(),
                ]);

                $screenSeats = ScreenSeat::where(
                    'screen_id',
                    $screen->id
                )->get();

                foreach ($screenSeats as $screenSeat) {
                    ShowtimeSeat::firstOrCreate(
                        [
                            'showtime_id' => $showtime->id,
                            'screen_seat_id' =>
                            $screenSeat->id,
                        ],
                        [
                            'id' => Str::uuid(),
                            'seat_status' => 'available',
                            'available' => true,
                            'price_at_showtime' =>
                            $screenSeat->price,
                        ]
                    );
                }
            }

            $current->addDay();
        }

        return $warnings;
    }

    public function editMovie($id)
    {
        $movie = Movie::with('genres')->findOrFail($id);

        $genres = Genre::orderBy('title')->get();
        $ageRatings = AgeRating::orderBy('title')->get();

        $cinemas = Cinema::orderBy('cinema_name')->get();

        $screens = Screen::with('cinema')
            ->orderBy('screen_number')
            ->get();

        $showtimes = Showtime::where('movie_id', $movie->id)
            ->where('start_time', '>=', now())
            ->with('screen.cinema')
            ->orderBy('start_time')
            ->get();

        return view(
            'admin.movies.edit',
            compact(
                'movie',
                'genres',
                'ageRatings',
                'cinemas',
                'screens',
                'showtimes'
            )
        );
    }

    public function updateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated =
            $request->validate($this->movieValidationRules());

        $genreIds = $validated['genre_ids'];
        unset($validated['genre_ids']);

        $validated['duration'] =
            (int) $validated['duration'];

        $validated['cast'] = !empty($validated['cast'])
            ? json_encode(array_filter($validated['cast']))
            : null;

        $validated['search_keywords'] =
            $validated['search_keywords'] ?? null;

        $validated['trailer_url'] =
            $this->convertYoutubeUrl(
                $validated['trailer_url'] ?? null
            );

        $validated['is_featured'] =
            $request->has('is_featured');

        $movie->update($validated);

        $movie->genres()->sync($genreIds);

        return redirect()
            ->route('admin.movies')
            ->with('success', 'Movie updated successfully.');
    }

    public function movieShowtimes($id)
    {
        $movie = Movie::findOrFail($id);

        $showtimes = Showtime::where('movie_id', $movie->id)
            ->with('screen.cinema')
            ->orderBy('start_time')
            ->get()
            ->map(function ($showtime) {
                return [
                    'id' => $showtime->id,
                    'cinema_name' => $showtime->screen->cinema->cinema_name ?? '—',
                    'screen_number' => $showtime->screen->screen_number ?? '—',
                    'date' => $showtime->start_time->format('Y-m-d'),
                    'start_time' => $showtime->start_time->format('H:i'),
                    'end_time' => $showtime->end_time->format('H:i'),
                    'is_active' => $showtime->is_active,
                ];
            });

        $screenSeats = ScreenSeat::where('screen_id', $screen->id)->get();

        foreach ($screenSeats as $screenSeat) {

            ShowtimeSeat::create([
                'id' => Str::uuid(),
                'showtime_id' => $showtime->id,
                'screen_seat_id' => $screenSeat->id,
                'seat_status' => 'available',
                'available' => true,
                'price_at_showtime' => $screenSeat->price,
            ]);
        }

        return response()->json($showtimes);
    }

    public function generateShowtimes(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'screen_id' => 'required|exists:screens,id',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'time_slots' => 'required|array|min:1|max:6',
            'time_slots.*' => 'nullable|date_format:H:i',
        ]);

        if (!$movie->released_date || !$movie->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Please set the movie release date and end date first.',
            ], 422);
        }

        $screen = Screen::findOrFail($validated['screen_id']);

        $durationMinutes = (int) $movie->duration;

        $created = 0;
        $skipped = 0;

        $current = Carbon::parse($movie->released_date)->startOfDay();
        $end = Carbon::parse($movie->end_date)->startOfDay();

        while ($current->lte($end)) {
            $dayOfWeek = (int) $current->format('w');

            if (!in_array($dayOfWeek, $validated['days'])) {
                $current->addDay();
                continue;
            }

            foreach ($validated['time_slots'] as $slot) {
                if (!$slot) {
                    continue;
                }

                $startsAt = Carbon::parse(
                    $current->format('Y-m-d') . ' ' . $slot
                );

                $endsAt = $startsAt
                    ->copy()
                    ->addMinutes($durationMinutes);

                $overlapExists = Showtime::where('screen_id', $screen->id)
                    ->where(function ($query) use ($startsAt, $endsAt) {
                        $query
                            ->where('start_time', '<', $endsAt)
                            ->where('end_time', '>', $startsAt);
                    })
                    ->exists();

                if ($overlapExists) {
                    $skipped++;
                    continue;
                }

                $showtime = Showtime::create([
                    'screen_id' => $screen->id,
                    'movie_id' => $movie->id,
                    'start_time' => $startsAt,
                    'end_time' => $endsAt,
                    'is_active' => true,
                    'created_by_id' => auth()->id(),
                ]);

                $created++;

                $screenSeats = ScreenSeat::where(
                    'screen_id',
                    $screen->id
                )->get();

                foreach ($screenSeats as $screenSeat) {
                    ShowtimeSeat::firstOrCreate(
                        [
                            'showtime_id' => $showtime->id,
                            'screen_seat_id' => $screenSeat->id,
                        ],
                        [
                            'id' => Str::uuid(),
                            'seat_status' => 'available',
                            'available' => true,
                            'price_at_showtime' => $screenSeat->price,
                        ]
                    );
                }
            }

            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
            'message' =>
            "{$created} showtime(s) created. "
                . "{$skipped} skipped because they overlap with existing showtimes.",
        ]);
    }

    public function deleteShowtime($id)
    {
        $showtime = Showtime::findOrFail($id);

        DB::transaction(function () use ($showtime) {

            ShowtimeSeat::where('showtime_id', $showtime->id)->delete();

            $showtime->delete();
        });

        return redirect()
            ->back()
            ->with('success', 'Showtime deleted successfully.');
    }

    // --------------------
    // Analytics
    // --------------------
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


    // --------------------
    // Reservations
    // --------------------
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

    public function markPaymentAsPaid(
        Payment $payment
    ) {
        if ($payment->payment_method !== 'onsite') {
            return back()->with(
                'error',
                'Only on-site payments can be updated manually.'
            );
        }

        if ($payment->payment_status !== 'pending') {
            return back()->with(
                'error',
                'This payment is not pending.'
            );
        }

        $payment->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with(
            'success',
            'Payment marked as paid successfully.'
        );
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
            'reservation_reference' =>
            $reservation->reservation_reference,

            'customer_name' =>
            $reservation->user?->username
                ?? trim(
                    ($reservation->guest_first_name ?? '')
                        . ' '
                        . ($reservation->guest_last_name ?? '')
                )
                ?: 'Guest',

            'customer_email' =>
            $reservation->user?->email
                ?? $reservation->guest_email
                ?? '—',

            'movie_title' =>
            $reservation->movie?->title ?? '—',

            'cinema_name' =>
            $reservation->cinema?->cinema_name ?? '—',

            'screen_number' =>
            $reservation->screen?->screen_number ?? '—',

            'showtime' =>
            $reservation->showtime?->start_time
                ?->format('Y-m-d H:i'),

            'seats' =>
            $reservation->seat_numbers,

            'total_seats' =>
            $reservation->total_seats,

            'subtotal' =>
            number_format(
                $reservation->subtotal,
                2
            ),

            'discount_amount' =>
            number_format(
                $reservation->discount_amount,
                2
            ),

            'final_amount' =>
            number_format(
                $reservation->final_amount,
                2
            ),

            'reservation_status' =>
            $reservation->reservation_status,

            'qr_code' =>
            $reservation->qr_code,

            'confirmed_at' =>
            $reservation->confirmed_at
                ?->format('Y-m-d H:i'),

            'cancelled_at' =>
            $reservation->cancelled_at
                ?->format('Y-m-d H:i'),

            // Payment
            'payment_id' =>
            $reservation->payment?->id,

            'payment_status' =>
            $reservation->payment?->payment_status
                ?? '—',

            'payment_method' =>
            $reservation->payment?->payment_method
                ?? '—',

            'transaction_id' =>
            $reservation->payment?->transaction_id
                ?? '—',

            'paid_at' =>
            $reservation->payment?->paid_at
                ?->format('Y-m-d H:i'),

            'can_mark_paid' =>
            $reservation->payment?->payment_method
                === 'onsite'
                && $reservation->payment?->payment_status
                === 'pending',
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

    // --------------------
    // Users
    // --------------------
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

    // --------------------
    // Settings
    // --------------------
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

    // --------------------
    // Coupons / Promotions
    // --------------------

    // Coupon
    public function couponsPromotions()
    {
        $coupons = Coupon::withCount('userCoupons')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'coupons_page');
        $promotions = Promotion::orderBy('created_at', 'desc')->paginate(10, ['*'], 'promotions_page');

        $genres = Genre::orderBy('title')->get();
        $movies = Movie::orderBy('title')->get();
        $cinemas = Cinema::orderBy('cinema_name')->get();

        return view('admin.coupons-promotions.index', compact(
            'coupons',
            'promotions',
            'genres',
            'movies',
            'cinemas'
        ));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'coupon_type' => 'required|string|in:percentage,fixed_amount',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['expires_at'])->endOfDay();
        }

        $validated['current_uses'] = 0;
        $validated['coupon_status'] = 'active';
        $validated['issued_at'] = now();
        $validated['issued_by_id'] = auth()->id();

        Coupon::create($validated);

        return redirect()->route('admin.coupons-promotions')->with('success', 'Coupon created successfully.');
    }

    public function toggleCouponStatus($id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->update([
            'coupon_status' => $coupon->coupon_status === 'active' ? 'disabled' : 'active',
        ]);

        return redirect()->route('admin.coupons-promotions')->with('success', 'Coupon status updated.');
    }

    public function distributeCoupon(Request $request, Coupon $coupon)
    {
        $users = User::where('role', 1);

        if ($request->target === 'selected') {
            $users->whereIn('id', $request->user_ids ?? []);
        }

        foreach ($users->get() as $user) {
            UserCoupon::firstOrCreate([
                'user_id' => $user->id,
                'coupon_id' => $coupon->id,
            ]);
        }

        return back()->with('success', 'Coupon distributed successfully.');
    }

    //Promotion
    public function storePromotion(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'applicable_genre_id' => 'nullable|exists:genres,id',
            'applicable_movie_id' => 'nullable|exists:movies,id',
            'applicable_cinema_id' => 'nullable|exists:cinemas,id',
            'max_uses' => 'nullable|integer|min:1',
            'min_ticket_purchase' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validated['type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()
                ->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%.'])
                ->withInput();
        }

        $validated['start_date'] = Carbon::parse($validated['start_date'])->startOfDay();
        $validated['end_date'] = Carbon::parse($validated['end_date'])->endOfDay();
        $validated['current_uses'] = 0;
        $validated['promotion_status'] = 'active';
        $validated['created_by_id'] = auth()->id();

        Promotion::create($validated);

        return redirect()
            ->route('admin.coupons-promotions')
            ->with('success', 'Promotion created successfully.')
            ->with('promotion_created', true);
    }

    public function togglePromotionStatus($id)
    {
        $promotion = Promotion::findOrFail($id);

        $promotion->update([
            'promotion_status' => $promotion->promotion_status === 'active' ? 'disabled' : 'active',
        ]);

        return redirect()->route('admin.coupons-promotions')->with('success', 'Promotion status updated.');
    }

    // --------------------
    // Reviews
    // --------------------
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'movie']);

        $status = $request->get('status', 'all');
        if ($status === 'visible') {
            $query->where('is_approved', true);
        } elseif ($status === 'hidden') {
            $query->where('is_approved', false);
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('movie', function ($mq) use ($search) {
                    $mq->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%");
                });
            });
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleReview($id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        return back()->with('success', 'Review status updated successfully.');
    }

    // --------------------
    // Infromations
    // --------------------
    public function information(Request $request)
    {
        $query = Information::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $category = $request->get('category', 'all');
        if ($category !== 'all') {
            $query->where('category_id', $category);
        }

        $categories = \App\Models\InformationCategory::orderBy('name')->get();

        $information = $query->paginate(20)->withQueryString();

        return view('admin.information.index', compact('information', 'categories'));
    }

    public function createInformation()
    {
        $categories = \App\Models\InformationCategory::orderBy('name')->get();
        return view('admin.information.create', compact('categories'));
    }

    public function storeInformation(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'category_id'  => 'required|exists:information_categories,id',
            'status'       => 'required|in:Draft,Published,Archived',
            'published_at' => 'required_if:status,Published|nullable|date',
            'image'        => 'nullable|image|max:2048',
        ]);

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(
                public_path('images/information'),
                $filename
            );
            $validated['image'] = 'images/information/' . $filename;
        }


        $validated['created_by_id'] = auth()->id();
        Information::create($validated);

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information added successfully.');
    }

    public function editInformation($id)
    {
        $information = Information::findOrFail($id);
        $categories = \App\Models\InformationCategory::orderBy('name')->get();
        return view('admin.information.edit', compact('information', 'categories'));
    }

    public function updateInformation(Request $request, $id)
    {
        $information = Information::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'category_id'  => 'required|exists:information_categories,id',
            'status'       => 'required|in:Draft,Published,Archived',
            'published_at' => 'required_if:status,Published|nullable|date',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {

            // delete previous image
            if ($information->image && file_exists(public_path($information->image))) {
                unlink(public_path($information->image));
            }

            // save new image
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(
                public_path('images/information'),
                $filename
            );
            $validated['image'] = 'images/information/' . $filename;
        }

        $information->update($validated);

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information updated successfully.');
    }

    public function informationDetails($id)
    {
        $information = Information::with('category')->findOrFail($id);

        return response()->json([
            'title'        => $information->title,
            'category'     => $information->category->name ?? '—',
            'status'       => $information->status,
            'content'      => $information->content,
            'published_at' => $information->published_at,
            'image'        => $information->image,
        ]);
    }

    public function deleteInformation($id)
    {
        $information = Information::findOrFail($id);

        $information->delete();

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information deleted successfully.');
    }

    //Information Category
    public function informationCategories()
    {
        $categories = \App\Models\InformationCategory::orderBy('name')->get();
        return view('admin.information.categories', compact('categories'));
    }

    public function storeInformationCategory(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:information_categories,name',
            'color' => 'required|string|max:20',
        ]);

        \App\Models\InformationCategory::create([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function deleteInformationCategory($id)
    {
        $category = \App\Models\InformationCategory::findOrFail($id);

        if ($category->informations()->count() > 0) {
            return back()->with('error', 'This category is in use and cannot be deleted.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }

    public function updateInformationCategory(Request $request, InformationCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:information_categories,name,' . $category->id,
            'color' => 'required|string|max:20',
        ]);

        $category->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }


    // Customer Service
    public function chat_index()
    {
        $conversations = Conversation::whereIn('status', [
            'waiting',
            'staff'
        ])
            ->with('user')
            ->orderByRaw("
            CASE
                WHEN status = 'waiting' THEN 0
                WHEN status = 'staff' THEN 1
                ELSE 2
            END
        ")
            ->latest('updated_at')
            ->paginate(10);
        
            $chatNotificationCount = Conversation::whereIn('status', [
                'waiting',
                'staff'
            ])->count();

        return view(
            'admin.chat.index',
            compact('conversations','chatNotificationCount')
        );
    }


    public function chat_show(Conversation $conversation)
    {
        // Load user
        $conversation->load('user');


        // =====================
        // Staff entered chat
        // =====================

        if ($conversation->status === 'waiting') {

            $conversation->update([
                'status' => 'staff'
            ]);
        }


        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get();


        return view('admin.chat.show', compact(
            'conversation',
            'messages'
        ));
    }


    public function chat_store(Request $request, Conversation $conversation)
    {
        Message::create([

            'conversation_id' => $conversation->id,

            'sender_type' => 'staff',

            'message' => $request->message

        ]);
        // Change to "Ready to Handle" status
        $conversation->update([
            'status' => 'staff'
        ]);


        return back();
    }

    public function chat_fetch(Conversation $conversation)
    {

        $messages =
            $conversation
            ->messages()
            ->orderBy('created_at')
            ->get();


        return response()->json([
            'messages' => $messages
        ]);
    }

    public function chat_close(Conversation $conversation)
    {
        // Delete messages
        $conversation->messages()->delete();


        // Change status
        $conversation->update([
            'status' => 'closed'
        ]);


        return redirect()->route('admin.chat.index');
    }
}
