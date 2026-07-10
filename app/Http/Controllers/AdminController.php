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
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Coupon;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\Information;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function movies()
    {
        Movie::syncStatuses();

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
        $cinemas = Cinema::orderBy('cinema_name')->get();
        $screens = Screen::with('cinema')->orderBy('screen_number')->get();

        return view('admin.movies.create', compact('genres', 'ageRatings', 'cinemas', 'screens'));
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

        $rules = $this->movieValidationRules();
        $rules['showtimes'] = 'nullable|array|max:6';
        $rules['showtimes.*.cinema_id'] = 'nullable|exists:cinemas,id';
        $rules['showtimes.*.screen_id'] = 'nullable|exists:screens,id';
        $rules['showtimes.*.date'] = 'nullable|date';
        $rules['showtimes.*.start_time'] = 'nullable';

        $validated = $request->validate($rules);

        $showtimesInput = $validated['showtimes'] ?? [];
        unset($validated['showtimes']);

        $validated['duration'] = (int) $validated['duration'];
        $validated['cast'] = $validated['cast'] ?? null;
        $validated['search_keywords'] = $validated['search_keywords'] ?? null;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['created_by_id'] = auth()->id();

        $movie = Movie::create($validated);

        $showtimeWarnings = $this->processShowtimes($movie, $showtimesInput);

        $message = 'Movie added successfully.';
        if (! empty($showtimeWarnings)) {
            $message .= ' However, some showtimes could not be created: ' . implode(' ', $showtimeWarnings);
        }

        return redirect()->route('admin.movies')->with('success', $message);
    }

    private function processShowtimes(Movie $movie, array $showtimesInput): array
    {
        $warnings = [];
        $durationMinutes = (int) $movie->duration;

        foreach ($showtimesInput as $index => $row) {

            $screenId = $row['screen_id'] ?? null;
            $date = $row['date'] ?? null;
            $startTime = $row['start_time'] ?? null;

            if (! $screenId || ! $date || ! $startTime) {
                continue;
            }

            $screen = Screen::find($screenId);

            if (! $screen) {
                $warnings[] = 'Showtime #' . ($index + 1) . ': screen not found.';
                continue;
            }

            $startsAt = Carbon::parse("{$date} {$startTime}");
            $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

            $alreadyExists = Showtime::where('screen_id', $screen->id)
                ->where('start_time', $startsAt)
                ->exists();

            if ($alreadyExists) {
                $warnings[] = 'Showtime #' . ($index + 1) . ': this screen already has a showtime at that exact time.';
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

            $screenSeats = ScreenSeat::where('screen_id', $screen->id)->get();

            foreach ($screenSeats as $screenSeat) {
                ShowtimeSeat::create([
                    'showtime_id' => $showtime->id,
                    'screen_seat_id' => $screenSeat->id,
                    'seat_status' => 'available',
                    'price_at_showtime' => $screenSeat->price,
                ]);
            }
        }

        return $warnings;
    }

    public function editMovie($id)
    {
        $movie = Movie::findOrFail($id);
        $genres = Genre::orderBy('title')->get();
        $ageRatings = AgeRating::orderBy('title')->get();
        $screens = Screen::with('cinema')->orderBy('screen_number')->get();

        return view('admin.movies.edit', compact('movie', 'genres', 'ageRatings', 'screens'));
    }

    public function updateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate($this->movieValidationRules());

        $validated['duration'] = (int) $validated['duration'];
        $validated['cast'] = $validated['cast'] ?? null;
        $validated['search_keywords'] = $validated['search_keywords'] ?? null;
        $validated['is_featured'] = $request->has('is_featured');

        $movie->update($validated);

        return redirect()->route('admin.movies')->with('success', 'Movie updated successfully.');
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
            'time_slots' => 'required|array|min:1',
            'time_slots.*' => 'nullable|date_format:H:i',
        ]);

        $screen = Screen::findOrFail($validated['screen_id']);
        $durationMinutes = (int) $movie->duration;
        $created = 0;
        $skipped = 0;

        $current = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();

        while ($current->lte($end)) {
            $dayOfWeek = (int) $current->format('w');

            if (in_array($dayOfWeek, $validated['days'])) {
                foreach ($validated['time_slots'] as $slot) {
                    if (! $slot) continue;

                    $startsAt = Carbon::parse($current->format('Y-m-d') . ' ' . $slot);
                    $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

                    $alreadyExists = Showtime::where('screen_id', $screen->id)
                        ->where('start_time', $startsAt)
                        ->exists();

                    if ($alreadyExists) {
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

                    $screenSeats = ScreenSeat::where('screen_id', $screen->id)->get();

                    dd($screenSeats->count());

                    foreach ($screenSeats as $screenSeat) {

                        ShowtimeSeat::create([
                            'id' => Str::uuid(),
                            'showtime_id' => $showtime->id,
                            'screen_seat_id' => $screenSeat->id,
                            'seat_status' => 'available',
                            'price_at_showtime' => $screenSeat->price,
                        ]);
                    }

                    $created++;
                }
            }

            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
            'message' => "{$created} showtime(s) created. {$skipped} skipped (already existed).",
        ]);
    }

    public function deleteShowtime($id)
    {
        $showtime = Showtime::findOrFail($id);
        $showtime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Showtime deleted successfully.',
        ]);
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

    public function couponsPromotions()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10, ['*'], 'coupons_page');
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

        $validated['current_uses'] = 0;
        $validated['promotion_status'] = 'active';
        $validated['created_by_id'] = auth()->id();

        Promotion::create($validated);

        return redirect()->route('admin.coupons-promotions')->with('success', 'Promotion created successfully.');
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

        $reviews = $query->paginate(20)->withQueryString();

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
        $query = Information::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $category = $request->get('category', 'all');
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $categories = Information::distinct()->pluck('category')->filter()->sort()->values();

        $information = $query->paginate(10)->withQueryString();

        return view('admin.information.index', compact('information', 'categories'));
    }

    public function createInformation()
    {
        return view('admin.information.create');
    }

    public function storeInformation(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'status' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $validated['created_by_id'] = auth()->id();

        Information::create($validated);

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information added successfully.');
    }

    public function editInformation($id)
    {
        $information = Information::findOrFail($id);

        return view('admin.information.edit', compact('information'));
    }

    public function updateInformation(Request $request, $id)
    {
        $information = Information::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'status' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $information->update($validated);

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information updated successfully.');
    }

    public function informationDetails($id)
    {
        $information = Information::findOrFail($id);

        return response()->json($information);
    }

    public function deleteInformation($id)
    {
        $information = Information::findOrFail($id);

        $information->delete();

        return redirect()
            ->route('admin.information')
            ->with('success', 'Information deleted successfully.');
    }


}
