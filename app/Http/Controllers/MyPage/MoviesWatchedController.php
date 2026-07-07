<?php
// app/Http/Controllers/MyPage/MoviesWatchedController.php
namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Mail\ReviewRequestMail;
use App\Models\Reservation;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MoviesWatchedController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $sort = $request->query('sort', 'recent'); // 'recent' or 'oldest'
        $startTimeSub = Showtime::select('start_time')
            ->whereColumn('showtimes.id', 'reservations.showtime_id');
        $query = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->with(['movie', 'showtime']);
        $query = $sort === 'oldest'
            ? $query->orderBy($startTimeSub)
            : $query->orderByDesc($startTimeSub);
        $watched = $query->paginate(5)->withQueryString();

        $reviewedMovieIds = $user->reviews()->pluck('movie_id')->all();

        $upcomingTicketsCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->count();
        $moviesWatchedCount = $user->reservations()
            ->where('reservation_status', 'confirmed')
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
            ->count();
        $reviewsWrittenCount = $user->reviews()->count();

        return view('mypage.movies.watched', [
            'user' => $user,
            'watched' => $watched,
            'reviewedMovieIds' => $reviewedMovieIds,
            'sort' => $sort,
            'upcomingTicketsCount' => $upcomingTicketsCount,
            'moviesWatchedCount' => $moviesWatchedCount,
            'reviewsWrittenCount' => $reviewsWrittenCount,
        ]);
    }

    /**
     * Send the "write a review" email for a specific watched reservation.
     */
    public function sendReviewEmail(Reservation $reservation): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($reservation->user_id === $user->id, 403);

        $movie = $reservation->movie;
        $watchedDate = optional($reservation->showtime)->start_time?->format('M d, Y');
        $reviewUrl = route('mypage.reviews.create', $movie->id);

        Mail::to($user->email)->send(
            new ReviewRequestMail($user, $movie, $watchedDate, $reviewUrl)
        );

        return back()->with('success', 'We sent you an email with a link to write your review.');
    }
}