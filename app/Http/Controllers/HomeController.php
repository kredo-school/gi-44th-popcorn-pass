<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Information;
use App\Models\Message;
use App\Models\Conversation;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * General Popcorn Pass Home
     */
    public function index()
    {
        $cinema = $this->getSelectedCinema();

        $data = $this->getHomeData($cinema);

        return view('home', $data);
    }

    /**
     * Cinema-specific Popcorn Pass Home
     *
     * Example:
     * /cinemas/{cinema}/home
     */
    public function cinemaHome(Cinema $cinema)
    {
        // Save selected cinema
        session([
            'selected_cinema_id' => $cinema->id,
        ]);

        $data = $this->getHomeData($cinema);

        $data['cinema'] = $cinema;

        return view('home', $data);
    }

    /**
     * Build Home page data.
     */
    private function getHomeData(?Cinema $cinema = null): array
    {
        /*
        |--------------------------------------------------------------------------
        | Now Showing
        |--------------------------------------------------------------------------
        */
        $moviesQuery = Movie::where('status', 'now_showing');

        if ($cinema) {
            $moviesQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where('cinemas.id', $cinema->id);
                }
            );
        }

        $movies = $moviesQuery
            ->orderBy('released_date', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Coming Soon
        |--------------------------------------------------------------------------
        */
        $comingSoonQuery = Movie::where('status', 'coming_soon')
            ->whereDate('released_date', '>=', now());

        if ($cinema) {
            $comingSoonQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where('cinemas.id', $cinema->id);
                }
            );
        }

        $comingSoonMovies = $comingSoonQuery
            ->orderBy('released_date', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Movies
        |--------------------------------------------------------------------------
        */
        $topMoviesQuery = Movie::withAvg('reviews', 'rating')
            ->where('status', 'now_showing');

        if ($cinema) {
            $topMoviesQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where('cinemas.id', $cinema->id);
                }
            );
        }

        $topMovies = $topMoviesQuery
            ->orderByDesc('review_average')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Hero Movie
        |--------------------------------------------------------------------------
        */
        $heroMovieQuery = Movie::where('status', 'coming_soon');

        if ($cinema) {
            $heroMovieQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where('cinemas.id', $cinema->id);
                }
            );
        }

        $heroMovie = $heroMovieQuery
            ->inRandomOrder()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Weekly Top Movie
        |--------------------------------------------------------------------------
        */
        $topMovieQuery = Movie::withAvg([
            'reviews as weekly_average' => function ($query) {
                $query->where(
                    'created_at',
                    '>=',
                    Carbon::now()->subWeek()
                );
            }
        ], 'rating');

        if ($cinema) {
            $topMovieQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where('cinemas.id', $cinema->id);
                }
            );
        }

        $topMovie = $topMovieQuery
            ->orderByDesc('weekly_average')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Information
        |--------------------------------------------------------------------------
        |
        | Currently shared across cinemas because Information does not
        | currently appear to have a cinema_id.
        |
        */
        $information = Information::where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(5)
            ->get();

        $information_slide = Information::with('category')
            ->where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Chat Notification
        |--------------------------------------------------------------------------
        */
        $unreadMessages = $this->getUnreadMessagesCount();

        return [
            'cinema' => $cinema,
            'movies' => $movies,
            'comingSoonMovies' => $comingSoonMovies,
            'topMovies' => $topMovies,
            'heroMovie' => $heroMovie,
            'topMovie' => $topMovie,
            'information' => $information,
            'information_slide' => $information_slide,
            'unreadMessages' => $unreadMessages,
        ];
    }

    /**
     * Common data for Showtime pages.
     */
    private function commonData($selectedDate): array
    {
        $dates = collect();

        for ($i = 0; $i < 14; $i++) {
            $dates->push(
                now()->copy()->addDays($i)
            );
        }

        $cinema = $this->getSelectedCinema();

        /*
        |--------------------------------------------------------------------------
        | Now Showing
        |--------------------------------------------------------------------------
        */
        $moviesQuery = Movie::with([
            'showtimes' => function ($query) use ($selectedDate, $cinema) {
                $query->whereDate(
                    'start_time',
                    $selectedDate
                );

                if ($cinema) {
                    $query->whereHas(
                        'screen.cinema',
                        function ($q) use ($cinema) {
                            $q->where(
                                'cinemas.id',
                                $cinema->id
                            );
                        }
                    );
                }
            },

            'showtimes.screen.cinema',
        ])
            ->where('status', 'now_showing');

        if ($cinema) {
            $moviesQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $movies = $moviesQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Coming Soon
        |--------------------------------------------------------------------------
        */
        $comingSoonQuery = Movie::where(
            'status',
            'coming_soon'
        )
            ->whereDate(
                'released_date',
                '>=',
                now()
            );

        if ($cinema) {
            $comingSoonQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $comingSoonMovies = $comingSoonQuery
            ->orderBy('released_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Hero Movie
        |--------------------------------------------------------------------------
        */
        $heroMovieQuery = Movie::where(
            'status',
            'coming_soon'
        );

        if ($cinema) {
            $heroMovieQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $heroMovie = $heroMovieQuery
            ->inRandomOrder()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Weekly Top Movie
        |--------------------------------------------------------------------------
        */
        $topMovieQuery = Movie::withAvg([
            'reviews as weekly_average' => function ($query) {
                $query->where(
                    'created_at',
                    '>=',
                    now()->subWeek()
                );
            }
        ], 'rating');

        if ($cinema) {
            $topMovieQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $topMovie = $topMovieQuery
            ->orderByDesc('weekly_average')
            ->first();

        $information = Information::where(
            'status',
            'Published'
        )
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(5)
            ->get();

        return [
            'cinema' => $cinema,
            'movies' => $movies,
            'comingSoonMovies' => $comingSoonMovies,
            'heroMovie' => $heroMovie,
            'topMovie' => $topMovie,
            'information' => $information,
            'dates' => $dates,
            'selectedDate' => $selectedDate,
        ];
    }

    /**
     * Showtime list.
     */
    public function showtime_display()
    {
        $selectedDate = request(
            'date',
            today()->format('Y-m-d')
        );

        $data = $this->commonData(
            $selectedDate
        );

        $data['isSearch'] = false;

        return view(
            'showtime-display.index',
            $data
        );
    }

    /**
     * Showtime search.
     */
    public function showtime_search(Request $request)
    {
        $selectedDate = $request->get(
            'date',
            now()->toDateString()
        );

        $keyword = $request->keyword;

        $cinema = $this->getSelectedCinema();

        $data = $this->commonData(
            $selectedDate
        );

        $searchQuery = Movie::with([
            'showtimes' => function ($query) use ($selectedDate, $cinema) {
                $query->whereDate(
                    'start_time',
                    $selectedDate
                );

                if ($cinema) {
                    $query->whereHas(
                        'screen.cinema',
                        function ($q) use ($cinema) {
                            $q->where(
                                'cinemas.id',
                                $cinema->id
                            );
                        }
                    );
                }
            },

            'showtimes.screen.cinema',
            'genres',
        ])
            ->where(function ($query) use ($keyword) {
                $query->where(
                    'title',
                    'like',
                    "%{$keyword}%"
                )
                    ->orWhere(
                        'director',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'synopsis',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhereHas(
                        'genres',
                        function ($q) use ($keyword) {
                            $q->where(
                                'title',
                                'like',
                                "%{$keyword}%"
                            );
                        }
                    )
                    ->orWhereJsonContains(
                        'search_keywords',
                        $keyword
                    );
            });

        if ($cinema) {
            $searchQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $data['searchResults'] =
            $searchQuery->get();

        $data['selectedDate'] =
            $selectedDate;

        $data['isSearch'] = true;

        return view(
            'layouts.showtime_display',
            $data
        );
    }

    /**
     * Showtime selection.
     */
    public function showtime_selection(Movie $movie)
    {
        $selectedDate = request(
            'date',
            today()->format('Y-m-d')
        );

        $cinema = $this->getSelectedCinema();

        $data = $this->commonData(
            $selectedDate
        );

        $movie->load([
            'showtimes' => function ($query) use ($selectedDate, $cinema) {
                $query->whereDate(
                    'start_time',
                    $selectedDate
                );

                if ($cinema) {
                    $query->whereHas(
                        'screen.cinema',
                        function ($q) use ($cinema) {
                            $q->where(
                                'cinemas.id',
                                $cinema->id
                            );
                        }
                    );
                }
            },

            'showtimes.screen.cinema',
        ]);

        $data['movie'] = $movie;
        $data['cinema'] = $cinema;
        $data['selectedDate'] = $selectedDate;
        $data['isSearch'] = false;

        return view(
            'reservations.showtime-selection',
            $data
        );
    }

    /**
     * Release page.
     */
    public function release(Movie $movie)
    {
        $movie->load('ageRating');

        return view(
            'movies.release',
            compact('movie')
        );
    }

    /**
     * Movie detail.
     */
    public function movie_detail(Movie $movie)
    {
        $cinema = $this->getSelectedCinema();

        $movie->load([
            'reviews' => function ($query) {
                $query->where(
                    'is_approved',
                    true
                );
            }
        ]);

        $averageRating =
            $movie->reviews->avg('rating') ?? 0;

        $totalReviews =
            $movie->reviews->count();

        $showtimeQuery = $movie
            ->showtimes()
            ->where(
                'is_active',
                true
            );

        if ($cinema) {
            $showtimeQuery->whereHas(
                'screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $showtime = $showtimeQuery
            ->orderBy('start_time')
            ->first();

        return view(
            'movies.movie_detail',
            compact(
                'movie',
                'averageRating',
                'totalReviews',
                'showtime',
                'cinema'
            )
        );
    }

    /**
     * Movie search.
     */
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $cinema = $this->getSelectedCinema();

        $moviesQuery = Movie::where(
            function ($query) use ($keyword) {
                $query->where(
                    'title',
                    'like',
                    "%{$keyword}%"
                )
                    ->orWhere(
                        'director',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'synopsis',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhereHas(
                        'genres',
                        function ($q) use ($keyword) {
                            $q->where(
                                'title',
                                'like',
                                "%{$keyword}%"
                            );
                        }
                    )
                    ->orWhereJsonContains(
                        'search_keywords',
                        $keyword
                    );
            }
        );

        if ($cinema) {
            $moviesQuery->whereHas(
                'showtimes.screen.cinema',
                function ($query) use ($cinema) {
                    $query->where(
                        'cinemas.id',
                        $cinema->id
                    );
                }
            );
        }

        $movies = $moviesQuery
            ->with('genres')
            ->get();

        return view(
            'movies.search',
            compact(
                'movies',
                'keyword',
                'cinema'
            )
        );
    }

    /**
     * Information index.
     */
    public function informationIndex()
    {
        $information = Information::where(
            'status',
            'Published'
        )
            ->whereNotNull('published_at')
            ->where(
                'published_at',
                '<=',
                now()
            )
            ->latest('published_at')
            ->paginate(10);

        return view(
            'information.index',
            compact('information')
        );
    }

    /**
     * Information detail.
     */
    public function informationDetail($id)
    {
        $information = Information::with(
            'category'
        )
            ->where(
                'status',
                'Published'
            )
            ->whereNotNull(
                'published_at'
            )
            ->where(
                'published_at',
                '<=',
                now()
            )
            ->findOrFail($id);

        return view(
            'information.information-detail',
            compact('information')
        );
    }

    /**
     * Get selected Popcorn Pass cinema.
     */
    private function getSelectedCinema(): ?Cinema
    {
        $cinemaId = session(
            'selected_cinema_id'
        );

        if (!$cinemaId) {
            return null;
        }

        $cinema = Cinema::where(
            'is_active',
            true
        )->find($cinemaId);

        if (!$cinema) {
            session()->forget(
                'selected_cinema_id'
            );

            return null;
        }

        return $cinema;
    }

    /**
     * Get unread chat notifications.
     */
    private function getUnreadMessagesCount(): int
    {
        if (!auth()->check()) {
            return 0;
        }

        $conversation = Conversation::where(
            'user_id',
            auth()->id()
        )->first();

        if (!$conversation) {
            return 0;
        }

        return Message::where(
            'conversation_id',
            $conversation->id
        )
            ->where(
                'sender_type',
                'staff'
            )
            ->where(
                'is_read',
                false
            )
            ->count();
    }
}