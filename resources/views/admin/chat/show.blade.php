@extends('layouts.admin')

@section('title', 'Admin Chat Show')
@section('page-title', 'Customer Chat Show')

@section('content')

    <div class="container py-5 show-box">


        {{-- Back Button --}}
        <div class="chat-back-btn">

            <a href="{{ route('admin.chat.index') }}" class="btn text-white border">Chat home</a>

        </div>

        <h2 class="bg-white mb-4 text-center text-dark p-3">
            👨‍💻 Chat with {{ $conversation->user->first_name }} {{ $conversation->user->last_name }}
        </h2>

        <div class="admin-chat-background">
            {{-- Chat Box --}}
            <div class="card shadow admin-chat-wrapper">


                {{-- Message Area --}}
                <div class="admin-chat-area " id="chat-area"
                    data-fetch-url="{{ route('admin.chat.fetch', $conversation->id) }}">


                    @foreach ($messages as $message)
                        <div class="admin-chat-message mb-3">


                            <strong>

                                @if ($message->sender_type === 'customer')
                                    👤 Customer
                                @elseif($message->sender_type === 'ai')
                                    🤖 AI
                                @else
                                    👨‍💻 Staff
                                @endif

                            </strong>


                            <p>
                                {{ $message->message }}
                            </p>
                            


                        </div>
                    @endforeach


                </div>


            </div>

            {{-- Reply --}}
            <form method="POST" action="{{ route('admin.chat.store', $conversation->id) }}" class="admin-chat-input">

                @csrf


                <div class="input-group mt-4">


                    <input type="text" name="message" class="form-control" placeholder="Reply message..." required>


                    <button class="btn btn-primary">

                        Send

                    </button>


                </div>


            </form>

            <form action={{ route('admin.chat.close', $conversation->id) }} method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-25 d-block mx-auto mt-5">
                    Chat Close
                </button>
            </form>
        </div>

    </div>



    {{-- Scroll Bottom After Reload --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            const chatArea = document.getElementById('chat-area');


            if (chatArea) {

                chatArea.scrollTop = chatArea.scrollHeight;

            }


        });
    </script>



    @vite('resources/js/admin/chat.js')


@endsection
