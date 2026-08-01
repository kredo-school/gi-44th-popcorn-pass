<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatRequest;

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

        $message = strtolower($request->message);

        $needStaff = false;

        // =====================
        // Gemini AI
        // =====================

        try {

            $response = Gemini::generativeModel('gemini-2.5-flash')
                ->generateContent(
                    "
                You are a customer support AI for a movie theater website.

                Answer questions about:
                - movies
                - showtimes
                - tickets
                - reservations
                - seats
                - cinema information

                If the customer has problems about:
                - payment
                - refund
                - reservation mistakes
                - complaints

                explain that a staff member will help.

                Keep answers short and friendly.

                Customer message:
                {$request->message}
                "
                );


            $reply = $response->text();
        } catch (\Exception $e) {


            $reply =
                "Sorry, our AI support is currently unavailable. A staff member will assist you.";

            $needStaff = true;
        }

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
