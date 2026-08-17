@extends('layouts.admin')

@section('title', 'Admin Chat Show')
@section('page-title', 'Customer Chat Show')

@section('content')

    <div class="admin-chat-show-page">

        {{-- Header --}}
        <div class="admin-chat-show-header">

            <div>
                <div class="admin-chat-subtitle">
                    Customer Support
                </div>

                <h1>
                    Customer Chat
                </h1>
            </div>

            <div class="admin-chat-customer">

                <div class="admin-chat-customer-icon">
                    👤
                </div>

                <div>
                    <span>Customer</span>

                    <strong>
                        {{ $conversation->user->first_name }}
                        {{ $conversation->user->last_name }}
                    </strong>
                </div>

            </div>

        </div>


        {{-- Chat Card --}}
        <div class="admin-chat-show-card">

            {{-- Chat Header --}}
            <div class="admin-chat-show-card-header">

                <div>

                    <h2>
                        💬 Support Conversation
                    </h2>

                    <p>
                        Customer support conversation
                    </p>

                </div>

            </div>


            {{-- Message Area --}}
            <div class="admin-chat-area" id="chat-area" data-fetch-url="{{ route('admin.chat.fetch', $conversation->id) }}">

                @foreach ($messages as $message)
                    <div class="admin-chat-message mb-3">

                        <strong>

                            @if ($message->sender_type === 'customer')
                                👤 Customer
                            @elseif ($message->sender_type === 'ai')
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


            {{-- Reply Area --}}
            <div class="admin-chat-reply">

                <form method="POST" action="{{ route('admin.chat.store', $conversation->id) }}">

                    @csrf

                    <div class="admin-chat-input-group">

                        <input type="text" name="message" class="admin-chat-input-field"
                            placeholder="Reply to customer..." required>

                        <button type="submit" class="admin-chat-send-btn">
                            Send
                            <span>→</span>
                        </button>

                    </div>

                </form>

            </div>


            {{-- Bottom Actions --}}
            <div class="admin-chat-actions">

                <a href="{{ route('admin.chat.index') }}" class="admin-chat-home-btn">
                    ← Chat Home
                </a>


                <form action="{{ route('admin.chat.close', $conversation->id) }}" method="POST">

                    @csrf

                    <button type="submit" class="admin-chat-end-btn">
                        End Chat
                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- Scroll Bottom --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const chatArea = document.getElementById('chat-area');

            if (chatArea) {
                chatArea.scrollTop = chatArea.scrollHeight;
            }

        });
    </script>

@endsection
