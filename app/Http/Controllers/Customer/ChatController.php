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
        $conversation = Conversation::firstOrCreate([
            'user_id' => auth()->id()
        ]);


        // Save customer message

        Message::create([

            'conversation_id' => $conversation->id,

            'sender_type' => 'customer',

            'message' => $request->message

        ]);

        $userMessage = $request->message;

        $message = strtolower($userMessage);

        $needStaff = false;

        $intent = 'unknown';


        // =====================
        // Intent classification
        // =====================
        if (
            str_contains($message, 'staff') ||
            str_contains($message, 'human') ||
            str_contains($message, 'operator') ||
            str_contains($message, 'refund') ||
            str_contains($message, 'payment') ||
            str_contains($message, 'complaint') ||
            str_contains($message, 'problem') ||
            str_contains($message, 'issue')
        ) {

            $intent = 'staff';
        } elseif (
            str_contains($message, 'change seat') ||
            str_contains($message, 'change my seat') ||
            str_contains($message, 'choose another seat') ||
            str_contains($message, 'switch seat') ||
            str_contains($message, 'talk to someone')
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

        // =====================
        // Intent classification
        // =====================

        if (
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
        }

        // =================================================
        //           Search movie information
        // =================================================




        $movieContext = '';
        $seatContext = '';
        $movieListContext = '';
        $reservationContext = '';

        // =====================
        // Reservation information
        // =====================


        if (
            str_contains($message, 'reservation') ||
            str_contains($message, 'booking') ||
            str_contains($message, 'ticket')
        ) {


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


        // =====================
        // Search movie title
        // =====================

        $movie = null;

        $words = explode(' ', $userMessage);


        foreach ($words as $word) {

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

        // If not found, search words inside message

        if (!$movie) {


            $words = explode(' ', strtolower($userMessage));

            foreach ($words as $word) {

                $word = trim($word);

                if (strlen($word) >= 3) {

                    $movie = Movie::where(
                        'title',
                        'like',
                        "%{$word}%"
                    )->first();

                    if ($movie) {
                        break;
                    }
                }
            }
        }


        // =====================
        // Movie information
        // =====================

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


            // =====================
            // Showtime
            // =====================

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


        // =====================
        // Seat information
        // =====================

        if (
            str_contains($message, 'seat') ||
            str_contains($message, 'available') ||
            str_contains($message, 'empty')
        ) {


            if ($movie) {

                $showtime = $movie->showtimes()
                    ->where('is_active', true)
                    ->orderBy('start_time')
                    ->first();
            } else {


                // Movie not found
                // Get latest active showtime

                $showtime = Showtime::where('is_active', true)
                    ->orderBy('start_time')
                    ->first();
            }



            if ($showtime) {


                $availableSeats = $showtime->showtimeSeats()
                    ->where(
                        'seat_status',
                        'available'
                    )
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



        // =====================
        // Currently showing movies
        // =====================

        if (
            str_contains($message, 'movie') ||
            str_contains($message, 'movies') ||
            str_contains($message, 'playing') ||
            str_contains($message, 'showing')
        ) {


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

        // =====================
        // Staff support
        // =====================

        if (
            str_contains($message, 'staff') ||
            str_contains($message, 'help') ||
            str_contains($message, 'complaint') ||
            str_contains($message, 'problem') ||
            str_contains($message, 'payment') ||
            str_contains($message, 'refund') ||
            str_contains($message, 'charged') ||
            str_contains($message, 'talk to someone') ||
            str_contains($message, 'human')
        ) {


            $staffContext = "

Staff Support:

A staff member will assist you with your request.

Please wait while we connect you to our support team.

";


            $needStaff = true;
        }

        // =====================
        // Generate AI response without Gemini
        // =====================

        $reply = '';


        if ($intent === 'staff') {

            $reply = '
            <span style="color:red;">
                    I will connect you with a staff member.
        
            <br><br>
            Please wait a moment while our support team reviews your request.<br><br>
            </span>
            ';

            $needStaff = true;
        }



        // Movie list question
        elseif ($intent === 'seat_change') {

            $reply = '
            You can change your seat before the movie starts.<br><br>

            <a href="' . route('mypage.dashboard') . '" class="btn btn-color mypage-text">
                My Page
            </a>
            ';
            $needStaff = false;

            // Reservation

        } elseif ($intent === 'reservation') {

            $reply =
                $reservationContext;
        }


        // Movie list

        elseif ($intent === 'movie_list') {

            $reply =
                "Currently showing movies:<br><br>"
                . nl2br($movieListContext)
                . '<br><br>
    <a href="' . route('movie.showtime.display') . '" class="btn btn-primary">
       View Showtime 
    </a>';
        }


        // Movie detail

        elseif ($intent === 'movie_detail') {


            if ($movie) {

                $reply =
                    '<img src="' . asset($movie->poster_url) . '" class="movie-poster w-100"><br><br>'
                    . nl2br($movieContext)
                    . '<br><br>
            <a href="' . route('movie_detail', ['movie' => $movie->id]) . '" class="btn btn-primary">
                View Movie Details
            </a>';
            } else {

                $reply =
                    "Sorry, I could not find the movie information. Please try another movie title.";
            }
        }


        // Seat

        elseif ($intent === 'seat') {

            $reply =
                $seatContext;
        } else {

            $reply =
                "Sorry, I could not find that information. A staff member will help. Please wait a minute.";

            $needStaff = true;
        }



        // =====================
        // Gemini AI
        // =====================

        // try {

        //     $response = Gemini::generativeModel('gemini-2.0-flash')
        //         ->generateContent(
        //             "

        //     You are an AI customer support assistant for a movie theater website.


        //     You can help customers with:

        //     - Movie information
        //     - Directors
        //     - Movie duration
        //     - Movie synopsis
        //     - Showtimes
        //     - Tickets
        //     - Reservations
        //     - Seat selection
        //     - Cinema information
        //     - Currently showing movies



        //     Database information:

        //     {$movieContext}
        //     {$movieListContext}
        //     {$seatContext}



        //     Customer question:

        //     {$request->message}



        //     Instructions:

        //     - Answer only in English.
        //     - Be friendly and professional.
        //     - Keep answers short and clear.
        //     - Use database information when available.
        //     - Do not make up movie information.
        //     - If the information is missing, tell the customer that staff can help.
        //     - For payment, refunds, complaints, or reservation problems, transfer the customer to staff.


        //     "
        //         );


        //     $reply = $response->text();
        // } catch (\Exception $e) {


        //     $reply =
        //         "Sorry, our AI support is currently unavailable. A staff member will assist you.";

        //     $needStaff = true;
        // }

        // =====================
        // Staff judgement
        // =====================

        if (
            str_contains($message, 'payment') ||
            str_contains($message, 'refund') ||
            str_contains($message, 'complaint') ||
            str_contains($message, 'charged') ||
            str_contains($message, 'problem')
        ) {

            $needStaff = true;
        }

        // =====================
        // Save AI message
        // =====================

        Message::create([

            'conversation_id' => $conversation->id,

            'sender_type' => 'ai',

            'message' => $reply

        ]);

        // =====================
        // Staff support
        // =====================

        if ($needStaff) {

            $conversation->update([

                'status' => 'waiting'

            ]);
            ChatRequest::updateOrCreate(
                [
                    'conversation_id' => $conversation->id
                ],

                [
                    'status' => 'pending'
                ]

            );
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
