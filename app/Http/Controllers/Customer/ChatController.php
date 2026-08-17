<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatRequest;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Reservation;


// Genemi API
use Gemini\Laravel\Facades\Gemini;

class ChatController extends Controller
{

    public function index()
    {
        $conversation = Conversation::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        if ($conversation->messages()->count() === 0) {

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'message' => 'Hello. How can I help you today?'
            ]);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return view(
            'customer.chat.index',
            compact(
                'conversation',
                'messages'
            )
        );
    }



    public function store(Request $request)
    {
        // =====================
        // Get conversation
        // =====================

        $conversation = Conversation::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        // =====================
        // Save customer message
        // =====================

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'message' => $request->message
        ]);

        // =================================================
        // IMPORTANT:
        // If staff is already handling this conversation,
        // do NOT generate any AI response.
        // =================================================

        if ($conversation->status === 'waiting') {

            return back();
        }

        $userMessage = $request->message;
        $message = strtolower($userMessage);

        $needStaff = false;
        $intent = 'unknown';

        // =================================================
        // Intent classification
        // =================================================

        if (
            str_contains($message, 'staff') ||
            str_contains($message, 'human') ||
            str_contains($message, 'operator') ||
            str_contains($message, 'refund') ||
            str_contains($message, 'payment') ||
            str_contains($message, 'complaint') ||
            str_contains($message, 'problem') ||
            str_contains($message, 'issue') ||
            str_contains($message, 'help') ||
            str_contains($message, 'charged') ||
            str_contains($message, 'talk to someone')
        ) {

            $intent = 'staff';
        } elseif (
            str_contains($message, 'change seat') ||
            str_contains($message, 'change my seat') ||
            str_contains($message, 'choose another seat') ||
            str_contains($message, 'switch seat')
        ) {

            $intent = 'seat_change';
        } elseif (
            str_contains($message, 'seat') ||
            str_contains($message, 'available') ||
            str_contains($message, 'empty')
        ) {

            $intent = 'seat';
        } elseif (
            str_contains($message, 'reservation') ||
            str_contains($message, 'booking') ||
            str_contains($message, 'ticket')
        ) {

            $intent = 'reservation';
        } elseif (
            str_contains($message, 'movie') ||
            str_contains($message, 'movies') ||
            str_contains($message, 'showing') ||
            str_contains($message, 'playing')
        ) {

            $intent = 'movie_list';
        } else {

            $intent = 'movie_detail';
        }

        // =================================================
        // Context
        // =================================================

        $movieContext = '';
        $seatContext = '';
        $movieListContext = '';
        $reservationContext = '';

        // =================================================
        // Reservation information
        // =================================================

        if ($intent === 'reservation') {

            $reservation = Reservation::where(
                'user_id',
                auth()->id()
            )
                ->latest()
                ->first();

            if ($reservation) {

                $reservationContext = "

Reservation Information:

Reservation Number:
{$reservation->reservation_reference}

Movie:
{$reservation->movie->title}

Showtime:
{$reservation->showtime->start_time->format('Y-m-d H:i')}

Status:
{$reservation->reservation_status}

Total Seats:
{$reservation->total_seats}

Final Amount:
{$reservation->final_amount}

";
            } else {

                $reservationContext = "

No reservation found.

";
            }
        }

        // =================================================
        // Search movie
        // =================================================

        $movie = null;

        $words = explode(' ', $userMessage);

        foreach ($words as $word) {

            $word = trim($word);

            if (strlen($word) >= 3) {

                $movie = Movie::where(
                    'title',
                    'like',
                    '%' . $word . '%'
                )->first();

                if ($movie) {
                    break;
                }
            }
        }

        // =================================================
        // Movie information
        // =================================================

        if ($movie) {

            $movieContext = "

Movie Information:

Title:
{$movie->title}

Director:
{$movie->director}

Synopsis:
{$movie->synopsis}

Duration:
{$movie->duration} minutes

";

            // =================================================
            // Showtime
            // =================================================

            if (
                str_contains($message, 'showtime') ||
                str_contains($message, 'time') ||
                str_contains($message, 'when') ||
                str_contains($message, 'schedule')
            ) {

                $showtimes = $movie->showtimes()
                    ->where('is_active', true)
                    ->orderBy('start_time')
                    ->get();

                $movieContext .= "

Available Showtimes:

";

                foreach ($showtimes as $showtime) {

                    $movieContext .=
                        $showtime->start_time->format('Y-m-d H:i')
                        . "\n";
                }
            }
        }

        // =================================================
        // Seat information
        // =================================================

        if ($intent === 'seat') {

            if ($movie) {

                $showtime = $movie->showtimes()
                    ->where('is_active', true)
                    ->orderBy('start_time')
                    ->first();
            } else {

                $showtime = Showtime::where('is_active', true)
                    ->orderBy('start_time')
                    ->first();
            }

            if ($showtime) {

                $availableSeats = $showtime->showtimeSeats()
                    ->where('seat_status', 'available')
                    ->count();

                $totalSeats = $showtime->showtimeSeats()
                    ->count();

                $seatContext = "

Seat Information:

Movie:
" . ($movie ? $movie->title : 'Unknown') . "

Showtime:
{$showtime->start_time->format('Y-m-d H:i')}

Available Seats:
{$availableSeats}

Total Seats:
{$totalSeats}

";
            }
        }

        // =================================================
        // Currently showing movies
        // =================================================

        if ($intent === 'movie_list') {

            $movies = Movie::where(
                'status',
                'now_showing'
            )
                ->orderBy('priority_order')
                ->get();

            if ($movies->count() > 0) {

                $movieListContext = "

Currently Showing Movies:

";

                foreach ($movies as $movieItem) {

                    $movieListContext .=
                        "- {$movieItem->title}\n";
                }
            }
        }

        // =================================================
        // Generate response
        // =================================================

        $reply = '';

        // =================================================
        // Staff handover
        // =================================================

        if ($intent === 'staff') {

            $reply = '
            <span style="color:red;">
                I will connect you with a staff member.
                <br><br>
                Please wait a moment while our support team reviews your request.
                <br><br>
            </span>
        ';

            $needStaff = true;
        }

        // =================================================
        // Seat change
        // =================================================

        elseif ($intent === 'seat_change') {

            $reply = '
            You can change your seat before the movie starts.
            <br><br>

            <a href="' . route('mypage.dashboard') . '"
               class="btn btn-color mypage-text">
                My Page
            </a>
        ';
        }

        // =================================================
        // Reservation
        // =================================================

        elseif ($intent === 'reservation') {

            $reply = $reservationContext;
        }

        // =================================================
        // Movie list
        // =================================================

        elseif ($intent === 'movie_list') {

            $reply =
                "Currently showing movies:<br><br>"
                . nl2br($movieListContext)
                . '<br><br>
            <a href="' . route('movie.showtime.display') . '"
               class="btn btn-primary">
                View Showtime
            </a>';
        }

        // =================================================
        // Movie detail
        // =================================================

        elseif ($intent === 'movie_detail') {

            if ($movie) {

                $reply =
                    '<img src="' . asset($movie->poster_url) . '"
                      class="movie-poster w-100">
                <br><br>'
                    . nl2br($movieContext)
                    . '<br><br>

                <a href="' . route(
                        'movie_detail',
                        ['movie' => $movie->id]
                    ) . '"
                   class="btn btn-primary">
                    View Movie Details
                </a>';
            } else {

                $reply =
                    "Sorry, I could not find the movie information. Please try another movie title.";
            }
        }

        // =================================================
        // Seat
        // =================================================

        elseif ($intent === 'seat') {

            $reply = $seatContext;
        }

        // =================================================
        // Unknown
        // =================================================

        else {

            $reply =
                "Sorry, I could not find that information. A staff member will help. Please wait a minute.";

            $needStaff = true;
        }

        // =================================================
        // Additional staff detection
        // =================================================

        if (
            str_contains($message, 'payment') ||
            str_contains($message, 'refund') ||
            str_contains($message, 'complaint') ||
            str_contains($message, 'charged') ||
            str_contains($message, 'problem') ||
            str_contains($message, 'issue') ||
            str_contains($message, 'staff') ||
            str_contains($message, 'human') ||
            str_contains($message, 'operator') ||
            str_contains($message, 'help') ||
            str_contains($message, 'talk to someone')
        ) {

            $needStaff = true;
        }

        // =================================================
        // Staff support
        // =================================================

        if ($needStaff) {

            // -----------------------------------------
            // Save AI handover message
            // -----------------------------------------

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'message' => $reply
            ]);

            // -----------------------------------------
            // Change conversation status
            // -----------------------------------------

            $conversation->update([
                'status' => 'waiting'
            ]);

            // -----------------------------------------
            // Create / update staff request
            // -----------------------------------------

            ChatRequest::updateOrCreate(
                [
                    'conversation_id' => $conversation->id
                ],
                [
                    'status' => 'pending'
                ]
            );
        } else {

            // =================================================
            // Save normal AI response
            // =================================================

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'message' => $reply
            ]);
        }

        return back();
    }






    public function staff()
    {

        Conversation::where(
            'user_id',
            auth()->id()
        )
            ->update([

                'status' => 'waiting'

            ]);


        return back();
    }






    public function fetch(Conversation $conversation)
    {
        // Prevent accessing another user's chat
        if (
            $conversation->user_id !== auth()->id()
        ) {
            abort(403);
        }

        $messages =

            $conversation
            ->messages()
            ->orderBy('created_at')
            ->get();

        return response()->json([

            'messages' => $messages

        ]);
    }

    public function close()
    {
        $conversation = Conversation::where(
            'user_id',
            auth()->id()
        )->first();


        if ($conversation) {

            // Delete mesaage
            $conversation->messages()->delete();


            // change status
            $conversation->update([
                'status' => 'closed'
            ]);
        }

        return back();
    }
}
