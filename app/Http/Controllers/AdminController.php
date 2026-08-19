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
    public function dashboard(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Dashboard Filters
    |--------------------------------------------------------------------------
    */

        $currentYear = now()->year;

        $availableYears = Payment::query()
            ->where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->selectRaw('YEAR(paid_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn($year) => (int) $year)
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$currentYear]);
        }

        $requestedYear = $request->integer('year');

        $selectedYear = $availableYears->contains($requestedYear)
            ? $requestedYear
            : $availableYears->first();

        $thisYear = $selectedYear;
        $lastYear = $thisYear - 1;

        /*
        $requestedYear = $request->query('year');

        $selectedYear = filter_var(
            $requestedYear,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 2000,
                    'max_range' => $currentYear,
                ],
            ]
        );

        if ($selectedYear === false) {
            $selectedYear = $currentYear;
        }

        $thisYear = $selectedYear;
        $lastYear = $thisYear - 1;

        $availableYears = range($currentYear, 2020);

        /*
    |--------------------------------------------------------------------------
    | Cinema Filter
    |--------------------------------------------------------------------------
    */

        $requestedCinemaId = $request->query('cinema_id');

        $selectedCinema = null;

        if ($requestedCinemaId) {
            $selectedCinema = Cinema::query()
                ->find($requestedCinemaId);
        }

        $cinemaId = $selectedCinema?->id;

        $cinemas = Cinema::query()
            ->orderBy('cinema_name')
            ->get([
                'id',
                'cinema_name',
            ]);

        /*
    |--------------------------------------------------------------------------
    | Revenue
    |--------------------------------------------------------------------------
    */

        $thisYearRevenueQuery = Payment::query()
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $thisYear);

        $lastYearRevenueQuery = Payment::query()
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $lastYear);

        if ($cinemaId) {
            $thisYearRevenueQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );

            $lastYearRevenueQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        $thisYearRevenue = (float) $thisYearRevenueQuery->sum('amount');
        $lastYearRevenue = (float) $lastYearRevenueQuery->sum('amount');

        $revenueChange = $lastYearRevenue > 0
            ? (($thisYearRevenue - $lastYearRevenue) / $lastYearRevenue) * 100
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

        if ($cinemaId) {
            $totalUsers = Reservation::query()
                ->where('cinema_id', $cinemaId)
                ->whereYear('created_at', $thisYear)
                ->whereNotNull('user_id')
                ->distinct()
                ->count('user_id');

            $lastYearUsers = Reservation::query()
                ->where('cinema_id', $cinemaId)
                ->whereYear('created_at', $lastYear)
                ->whereNotNull('user_id')
                ->distinct()
                ->count('user_id');
        } else {
            $totalUsers = User::query()
                ->whereYear('created_at', $thisYear)
                ->count();

            $lastYearUsers = User::query()
                ->whereYear('created_at', $lastYear)
                ->count();
        }

        $userChange = $lastYearUsers > 0
            ? (($totalUsers - $lastYearUsers) / $lastYearUsers) * 100
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Active Movies
    |--------------------------------------------------------------------------
    */

        Movie::syncStatuses();

        $activeMoviesQuery = Movie::query()
            ->where('status', 'now_showing');

        if ($cinemaId) {
            $activeMoviesQuery->whereHas(
                'showtimes.screen',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        $activeMovies = $activeMoviesQuery->count();

        $movieChange = 0;

        /*
    |--------------------------------------------------------------------------
    | Reservations
    |--------------------------------------------------------------------------
    */

        $thisYearReservationsQuery = Reservation::query()
            ->whereYear('created_at', $thisYear);

        $lastYearReservationsQuery = Reservation::query()
            ->whereYear('created_at', $lastYear);

        if ($cinemaId) {
            $thisYearReservationsQuery->where(
                'cinema_id',
                $cinemaId
            );

            $lastYearReservationsQuery->where(
                'cinema_id',
                $cinemaId
            );
        }

        $totalReservations = $thisYearReservationsQuery->count();
        $lastYearReservations = $lastYearReservationsQuery->count();

        $reservationChange = $lastYearReservations > 0
            ? (($totalReservations - $lastYearReservations) / $lastYearReservations) * 100
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Revenue Trend
    |--------------------------------------------------------------------------
    */

        $monthlyRevenueQuery = Payment::query()
            ->selectRaw(
                'MONTH(paid_at) as month, SUM(amount) as total'
            )
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $thisYear);

        if ($cinemaId) {
            $monthlyRevenueQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        $monthlyRevenue = $monthlyRevenueQuery
            ->groupByRaw('MONTH(paid_at)')
            ->orderByRaw('MONTH(paid_at)')
            ->get();

        $revenueData = array_fill(0, 12, 0);

        foreach ($monthlyRevenue as $item) {
            $monthIndex = (int) $item->month - 1;

            if ($monthIndex >= 0 && $monthIndex < 12) {
                $revenueData[$monthIndex] = (float) $item->total;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Top Performing Movies
    |--------------------------------------------------------------------------
    */

        $movieSalesRankingQuery = Payment::query()
            ->join(
                'reservations',
                'payments.reservation_id',
                '=',
                'reservations.id'
            )
            ->join(
                'movies',
                'reservations.movie_id',
                '=',
                'movies.id'
            )
            ->where(
                'payments.payment_status',
                'paid'
            )
            ->whereYear(
                'payments.paid_at',
                $thisYear
            );

        if ($cinemaId) {
            $movieSalesRankingQuery->where(
                'reservations.cinema_id',
                $cinemaId
            );
        }

        $movieSalesRanking = $movieSalesRankingQuery
            ->select(
                'movies.id',
                'movies.title',
                DB::raw(
                    'SUM(payments.amount) as total_sales'
                )
            )
            ->groupBy(
                'movies.id',
                'movies.title'
            )
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Recent Reservations
    |--------------------------------------------------------------------------
    */

        $recentReservationsQuery = Reservation::query()
            ->with([
                'user',
                'movie',
                'cinema',
                'screen',
                'showtime',
                'payment',
            ])
            ->whereYear('created_at', $thisYear);

        if ($cinemaId) {
            $recentReservationsQuery->where(
                'cinema_id',
                $cinemaId
            );
        }

        $recentReservations = $recentReservationsQuery
            ->latest('created_at')
            ->limit(5)
            ->get();

        /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.dashboard',
            compact(
                'currentYear',
                'thisYear',
                'lastYear',
                'availableYears',
                'cinemas',
                'cinemaId',
                'selectedCinema',
                'thisYearRevenue',
                'revenueChange',
                'totalUsers',
                'userChange',
                'activeMovies',
                'movieChange',
                'totalReservations',
                'reservationChange',
                'revenueData',
                'movieSalesRanking',
                'recentReservations'
            )
        );
    }

    // --------------------
    // Movies
    // --------------------
    public function movies(Request $request)
    {
        Movie::syncStatuses();

        $query = Movie::with(['genres', 'ageRating']);

        // Movie search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('title', 'like', '%' . $search . '%');
        }

        // Genre filter
        if ($request->filled('genre_id') && $request->genre_id !== 'all') {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $movies = $query
            ->orderByRaw("
            CASE status
                WHEN 'now_showing' THEN 1
                WHEN 'coming_soon' THEN 2
                WHEN 'archived' THEN 3
                ELSE 4
            END
        ")
            ->orderBy('released_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Genre
        $genres = Genre::orderBy('title')->get();

        // Status
        $statusOptions = Movie::query()
            ->whereNotNull('status')
            ->distinct()
            ->pluck('status');

        return view('admin.movies.index', compact(
            'movies',
            'genres',
            'statusOptions'
        ));
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
            'genres' => $movie->genres->pluck('title'),
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
            'trailer_url' => 'required|url',
            'poster_url' => 'required|url',
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

        $videoId = null;

        // youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        // youtube.com/watch?v=VIDEO_ID
        elseif (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        // youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([^?&]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if (!$videoId) {
            return $url;
        }

        return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&playlist={$videoId}";
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
            ? array_values(array_filter($validated['cast']))
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

    public function archive(Movie $movie)
    {
        $showtimes = $movie->showtimes()
            ->whereDate('start_time', '>=', Carbon::today())
            ->get();

        foreach ($showtimes as $showtime) {

            foreach ($showtime->reservations as $reservation) {

                // Delete Payment
                $reservation->payment()?->delete();

                // Delete ReservationSeat
                $reservation->reservationSeats()->delete();

                // Delete Reservation
                $reservation->delete();
            }

            // Delete ShowtimeSeat
            $showtime->showtimeSeats()->delete();

            // Delete Showtime
            $showtime->delete();
        }


        // status->Archive
        $movie->update([
            'status' => 'archived',
        ]);


        return response()->json([
            'success' => true,
        ]);
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

        // All active cinemas must be available even when the movie
        // does not have any showtimes yet.
        $cinemas = Cinema::where('is_active', true)
            ->orderBy('cinema_name')
            ->get();

        // If future showtimes already exist, use the first one only as
        // the initial cinema selection in the edit screen.
        $cinema = Showtime::where('movie_id', $movie->id)
            ->where('start_time', '>=', now())
            ->with('screen.cinema')
            ->orderBy('start_time')
            ->first()
            ?->screen
            ?->cinema;

        // Screens for the initially selected cinema.
        $screens = $cinema
            ? Screen::where('cinema_id', $cinema->id)
            ->where('is_active', true)
            ->orderBy('screen_number')
            ->get()
            : collect();

        // All active screens are also passed to the Blade view so that
        // JavaScript can instantly filter them whenever Cinema changes.
        $allScreens = Screen::where('is_active', true)
            ->orderBy('screen_number')
            ->get([
                'id',
                'cinema_id',
                'screen_number',
                'screen_name',
                'screen_type',
            ]);

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
                'cinema',
                'screens',
                'allScreens',
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
            ? array_values(array_filter($validated['cast']))
            : null;

        $validated['search_keywords'] =
            $validated['search_keywords'] ?? null;

        $validated['trailer_url'] =
            $this->convertYoutubeUrl(
                $validated['trailer_url'] ?? null
            );

        $validated['is_featured'] =
            $request->has('is_featured');


        // ==========================
        // Movie Status change automaticaly
        // ==========================

        $today = now()->toDateString();

        if (
            !empty($validated['end_date']) &&
            $validated['end_date'] < $today
        ) {

            // archive
            $validated['status'] = 'archived';
        } elseif (
            !empty($validated['released_date']) &&
            $validated['released_date'] <= $today
        ) {

            // nowshowing
            $validated['status'] = 'now_showing';
        } else {

            // coming soon
            $validated['status'] = 'coming_soon';
        }


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
                    'cinema_name' => $showtime->screen?->cinema?->cinema_name ?? '—',
                    'screen_number' => $showtime->screen?->screen_number ?? '—',
                    'date' => $showtime->start_time->format('Y-m-d'),
                    'start_time' => $showtime->start_time->format('H:i'),
                    'end_time' => $showtime->end_time->format('H:i'),
                    'is_active' => $showtime->is_active,
                ];
            });

        return response()->json($showtimes);
    }

    public function generateShowtimes(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'screen_id' => 'required|exists:screens,id',

            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',

            'time_slots' => 'required|array|min:1|max:6',
            'time_slots.*' => 'nullable|date_format:H:i',
        ]);


        $screen = Screen::findOrFail($validated['screen_id']);


        $durationMinutes = (int) $movie->duration;


        $created = 0;
        $skipped = 0;


        $current = Carbon::parse($validated['start_date'])
            ->startOfDay();

        $end = Carbon::parse($validated['end_date'])
            ->startOfDay();



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
    public function analytics(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

        $currentYear = now()->year;

        $availableYears = Payment::query()
            ->where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->selectRaw('YEAR(paid_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn($year) => (int) $year)
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$currentYear]);
        }

        $requestedYear = $request->integer('year');

        $selectedYear = $availableYears->contains($requestedYear)
            ? $requestedYear
            : $availableYears->first();


        $requestedCinemaId = $request->query('cinema_id');

        $selectedCinema = null;

        if ($requestedCinemaId) {
            $selectedCinema = Cinema::query()
                ->find($requestedCinemaId);
        }

        $cinemaId = $selectedCinema?->id;

        $cinemas = Cinema::query()
            ->orderBy('cinema_name')
            ->get([
                'id',
                'cinema_name',
            ]);

        /*
    |--------------------------------------------------------------------------
    | Base Paid Payments Query
    |--------------------------------------------------------------------------
    */

        $paidPaymentsQuery = Payment::query()
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $selectedYear);

        if ($cinemaId) {
            $paidPaymentsQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        /*
    |--------------------------------------------------------------------------
    | KPI - Total Revenue
    |--------------------------------------------------------------------------
    */

        $totalRevenue = (float) (clone $paidPaymentsQuery)
            ->sum('amount');

        /*
    |--------------------------------------------------------------------------
    | KPI - Paid Reservations
    |--------------------------------------------------------------------------
    */

        $totalReservations = (clone $paidPaymentsQuery)
            ->distinct()
            ->count('reservation_id');

        /*
    |--------------------------------------------------------------------------
    | KPI - Customers
    |--------------------------------------------------------------------------
    |
    | Registered customers only.
    | Guest reservations are not included in this unique user count.
    |
    */

        $customersQuery = Payment::query()
            ->join(
                'reservations',
                'payments.reservation_id',
                '=',
                'reservations.id'
            )
            ->where(
                'payments.payment_status',
                'paid'
            )
            ->whereYear(
                'payments.paid_at',
                $selectedYear
            )
            ->whereNotNull(
                'reservations.user_id'
            );

        if ($cinemaId) {
            $customersQuery->where(
                'reservations.cinema_id',
                $cinemaId
            );
        }

        $totalCustomers = $customersQuery
            ->distinct()
            ->count('reservations.user_id');

        /*
    |--------------------------------------------------------------------------
    | KPI - Average Revenue Per Reservation
    |--------------------------------------------------------------------------
    */

        $avgRevenuePerReservation = $totalReservations > 0
            ? $totalRevenue / $totalReservations
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Monthly Revenue Trend
    |--------------------------------------------------------------------------
    */

        $monthlyRevenueQuery = Payment::query()
            ->selectRaw(
                'MONTH(paid_at) as month, SUM(amount) as total'
            )
            ->where(
                'payment_status',
                'paid'
            )
            ->whereYear(
                'paid_at',
                $selectedYear
            );

        if ($cinemaId) {
            $monthlyRevenueQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        $monthlyRevenueRows = $monthlyRevenueQuery
            ->groupByRaw(
                'MONTH(paid_at)'
            )
            ->orderByRaw(
                'MONTH(paid_at)'
            )
            ->get();

        $monthlyRevenueData = array_fill(
            0,
            12,
            0
        );

        foreach ($monthlyRevenueRows as $row) {
            $monthIndex = (int) $row->month - 1;

            if ($monthIndex >= 0 && $monthIndex < 12) {
                $monthlyRevenueData[$monthIndex] =
                    (float) $row->total;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Monthly Reservation Trend
    |--------------------------------------------------------------------------
    |
    | Only reservations with completed paid payments are counted so that
    | reservation and revenue analytics use the same business definition.
    |
    */

        $monthlyReservationsQuery = Payment::query()
            ->selectRaw(
                'MONTH(payments.paid_at) as month,
             COUNT(DISTINCT payments.reservation_id) as total'
            )
            ->join(
                'reservations',
                'payments.reservation_id',
                '=',
                'reservations.id'
            )
            ->where(
                'payments.payment_status',
                'paid'
            )
            ->whereYear(
                'payments.paid_at',
                $selectedYear
            );

        if ($cinemaId) {
            $monthlyReservationsQuery->where(
                'reservations.cinema_id',
                $cinemaId
            );
        }

        $monthlyReservationRows = $monthlyReservationsQuery
            ->groupByRaw(
                'MONTH(payments.paid_at)'
            )
            ->orderByRaw(
                'MONTH(payments.paid_at)'
            )
            ->get();

        $monthlyReservationData = array_fill(
            0,
            12,
            0
        );

        foreach ($monthlyReservationRows as $row) {
            $monthIndex = (int) $row->month - 1;

            if ($monthIndex >= 0 && $monthIndex < 12) {
                $monthlyReservationData[$monthIndex] =
                    (int) $row->total;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Top Performing Movies
    |--------------------------------------------------------------------------
    |
    | Revenue source is now Payment.amount, matching all other revenue
    | analytics and the Admin Dashboard.
    |
    */

        $topMoviesQuery = Payment::query()
            ->join(
                'reservations',
                'payments.reservation_id',
                '=',
                'reservations.id'
            )
            ->join(
                'movies',
                'reservations.movie_id',
                '=',
                'movies.id'
            )
            ->where(
                'payments.payment_status',
                'paid'
            )
            ->whereYear(
                'payments.paid_at',
                $selectedYear
            );

        if ($cinemaId) {
            $topMoviesQuery->where(
                'reservations.cinema_id',
                $cinemaId
            );
        }

        $topMovies = $topMoviesQuery
            ->select(
                'movies.id',
                'movies.title',
                DB::raw(
                    'SUM(payments.amount) as total_revenue'
                ),
                DB::raw(
                    'COUNT(DISTINCT reservations.id) as reservation_count'
                )
            )
            ->groupBy(
                'movies.id',
                'movies.title'
            )
            ->orderByDesc(
                'total_revenue'
            )
            ->limit(5)
            ->get();
        /*
|--------------------------------------------------------------------------
| Daily Revenue Chart
|--------------------------------------------------------------------------
*/

        $dailyRevenueQuery = Payment::query()
            ->selectRaw(
                'DATE(paid_at) as date, SUM(amount) as total'
            )
            ->where(
                'payment_status',
                'paid'
            )
            ->whereYear(
                'paid_at',
                $selectedYear
            );

        if ($cinemaId) {
            $dailyRevenueQuery->whereHas(
                'reservation',
                function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                }
            );
        }

        $dailyRevenueChart = $dailyRevenueQuery
            ->groupByRaw('DATE(paid_at)')
            ->orderByRaw('DATE(paid_at)')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Cinema Performance
    |--------------------------------------------------------------------------
    |
    | This comparison is only required when the administrator is viewing
    | all cinemas.
    |
    */

        $cinemaPerformance = collect();

        if (!$cinemaId) {
            $cinemaPerformance = Payment::query()
                ->join(
                    'reservations',
                    'payments.reservation_id',
                    '=',
                    'reservations.id'
                )
                ->join(
                    'cinemas',
                    'reservations.cinema_id',
                    '=',
                    'cinemas.id'
                )
                ->where(
                    'payments.payment_status',
                    'paid'
                )
                ->whereYear(
                    'payments.paid_at',
                    $selectedYear
                )
                ->select(
                    'cinemas.id',
                    'cinemas.cinema_name',
                    DB::raw(
                        'SUM(payments.amount) as total_revenue'
                    ),
                    DB::raw(
                        'COUNT(DISTINCT reservations.id) as reservation_count'
                    ),
                    DB::raw(
                        'COUNT(DISTINCT reservations.user_id) as customer_count'
                    )
                )
                ->groupBy(
                    'cinemas.id',
                    'cinemas.cinema_name'
                )
                ->orderByDesc(
                    'total_revenue'
                )
                ->get();

            /*
        |--------------------------------------------------------------------------
        | Daily Revenue Chart
        |--------------------------------------------------------------------------
        */
            $dailyRevenueQuery = Payment::query()
                ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
                ->where('payment_status', 'paid')
                ->whereYear('paid_at', $selectedYear);

            if ($cinemaId) {
                $dailyRevenueQuery->whereHas('reservation', function ($query) use ($cinemaId) {
                    $query->where('cinema_id', $cinemaId);
                });
            }

            $dailyRevenueChart = $dailyRevenueQuery
                ->groupByRaw('DATE(paid_at)')
                ->orderByRaw('DATE(paid_at)')
                ->get();

            /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

            return view(
                'admin.analytics.index',
                compact(
                    'currentYear',
                    'selectedYear',
                    'availableYears',
                    'cinemas',
                    'cinemaId',
                    'selectedCinema',
                    'totalRevenue',
                    'totalReservations',
                    'totalCustomers',
                    'avgRevenuePerReservation',
                    'monthlyRevenueData',
                    'monthlyReservationData',
                    'topMovies',
                    'cinemaPerformance',
                    'dailyRevenueChart',
                )
            );
        }
        return view(
            'admin.analytics.index',
            compact(
                'currentYear',
                'selectedYear',
                'availableYears',
                'cinemas',
                'cinemaId',
                'selectedCinema',
                'totalRevenue',
                'totalReservations',
                'totalCustomers',
                'avgRevenuePerReservation',
                'monthlyRevenueData',
                'monthlyReservationData',
                'dailyRevenueChart',
                'topMovies',
                'cinemaPerformance'
            )
        );
    }


    // --------------------
    // Reservations
    // --------------------
    private function reservationStatusOptions(): array
    {
        return ['confirmed', 'partially_cancelled', 'cancelled', 'expired'];
    }

    private function paymentStatusOptions(): array
    {
        return ['pending', 'paid', 'cancelled', 'expired', 'failed'];
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

        // Reservation status
        if (
            $request->filled('status')
            && $request->get('status') !== 'all'
        ) {
            $status = $request->get('status');

            if ($status === 'partially_cancelled') {
                $query
                    ->where(
                        'reservation_status',
                        '!=',
                        'cancelled'
                    )
                    ->whereHas(
                        'reservationSeats',
                        function ($seatQuery) {
                            $seatQuery->whereNotNull(
                                'cancelled_at'
                            );
                        }
                    )
                    ->whereHas(
                        'reservationSeats',
                        function ($seatQuery) {
                            $seatQuery->whereNull(
                                'cancelled_at'
                            );
                        }
                    );
            } else {
                $query->where(
                    'reservation_status',
                    $status
                );
            }
        }

        // Payment status
        if (
            $request->filled('payment_status')
            && $request->get('payment_status') !== 'all'
        ) {
            $query->whereHas('payment', function ($paymentQuery) use ($request) {
                $paymentQuery->where(
                    'payment_status',
                    $request->get('payment_status')
                );
            });
        }

        if (
            $request->filled('cinema_id')
            && $request->get('cinema_id') !== 'all'
        ) {
            $query->where(
                'cinema_id',
                $request->get('cinema_id')
            );
        }

        return $query->orderByDesc('created_at');
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
        $paymentStatusOptions = $this->paymentStatusOptions();

        return view(
            'admin.reservations.index',
            compact(
                'reservations',
                'cinemas',
                'statusOptions',
                'paymentStatusOptions'
            )
        );
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

        $seatDetails = $reservation->reservationSeats
            ->map(function ($reservationSeat) {
                $screenSeat =
                    $reservationSeat->showtimeSeat?->screenSeat;

                $seatNumber =
                    $screenSeat?->seat_number
                    ?? (
                        ($screenSeat?->seat_row ?? '')
                        . ($screenSeat?->seat_position ?? '')
                    );

                return [
                    'seat_number' => $seatNumber ?: '—',

                    'status' => $reservationSeat->cancelled_at
                        ? 'cancelled'
                        : 'active',

                    'cancelled_at' =>
                    $reservationSeat->cancelled_at
                        ?->format('Y-m-d H:i'),
                ];
            })
            ->values();

        $activeSeatCount = $reservation->reservationSeats
            ->whereNull('cancelled_at')
            ->count();

        $cancelledSeatCount = $reservation->reservationSeats
            ->whereNotNull('cancelled_at')
            ->count();

        $isPartiallyCancelled =
            $reservation->reservation_status !== 'cancelled'
            && $activeSeatCount > 0
            && $cancelledSeatCount > 0;

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

            'seats' => $seatDetails,

            'active_seat_count' => $activeSeatCount,

            'cancelled_seat_count' => $cancelledSeatCount,

            'original_seat_count' => $activeSeatCount + $cancelledSeatCount,

            'total_seats' => $reservation->total_seats,

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

            'reservation_status' => $reservation->reservation_status,

            'display_status' => $isPartiallyCancelled ? 'partially_cancelled' : $reservation->reservation_status,

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
                    $reservation->user->username ?? 'Guest',
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
            2 => 'Admin',
            3 => 'Manager',
            4 => 'Support',
            1 => 'Customer',
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
            $query->where(
                'is_active',
                $request->get('status') === 'active' ? 1 : 0
            );
        }

        $users = $query
            ->orderByRaw("
            CASE role
                WHEN 2 THEN 1
                WHEN 3 THEN 2
                WHEN 4 THEN 3
                WHEN 1 THEN 4
                ELSE 5
            END
        ")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $roleOptions = $this->userRoleOptions();

        return view('admin.users.index', compact(
            'users',
            'roleOptions'
        ));
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
        $query = Information::with('category');

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

        $information = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'Draft' THEN 1
                    WHEN status = 'Published' THEN 2
                    WHEN status = 'Archived' THEN 3
                    ELSE 4
                END
            ")
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(20)
            ->withQueryString();

        return view('admin.information.index', compact(
            'information',
            'categories'
        ));
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
            compact('conversations', 'chatNotificationCount')
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
