@extends('layouts.app')

@section('title', 'Chat')

@section('content')

    <div class="chat-page">
        <div class="container">

            <div class="card shadow mx-auto chat-wrapper">


                {{-- Header --}}
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                    <h3 class="mb-0">
                        🤖 AI Customer Support
                    </h3>

                </div>



                {{-- Chat Messages --}}
                <div class="card-body chat-area" id="chat-area"
                    data-fetch-url="{{ route('customer.chat.fetch', $conversation->id) }}">

                    @foreach ($messages as $message)
                        {{-- AI --}}
                        @if ($message->sender_type === 'ai')
                            <div class="message mb-4">
                                <div class="fw-bold">
                                    🤖 AI
                                </div>

                                <div class="bubble ai">
                                    {!! $message->message !!}
                                </div>
                            </div>

                            {{-- Customer --}}
                        @elseif($message->sender_type === 'customer')
                            <div class="message mb-4 text-end">

                                <div class="fw-bold">
                                    👤 You
                                </div>

                                <div class="bubble customer">
                                    {{ $message->message }}
                                </div>

                            </div>

                            {{-- Staff --}}
                        @elseif($message->sender_type === 'staff')
                            <div class="message mb-4">

                                <div class="fw-bold">
                                    👨‍💻 Staff
                                </div>

                                <div class="bubble staff">
                                    {{ $message->message }}
                                </div>

                            </div>
                        @endif
                    @endforeach

                </div>


                {{-- Staff Request --}}
                @if ($conversation->status === 'ai')
                    <form action="{{ route('customer.chat.staff') }}" method="POST">

                        @csrf

                        <button class="btn btn-outline-primary w-75">

                            👨‍💻 Contact Staff

                        </button>

                    </form>
                @elseif($conversation->status === 'waiting')
                    <div class="text-center text-secondary">
                        👨‍💻 Waiting for staff response...
                    </div>
                @elseif($conversation->status === 'staff')
                    <div class="text-center text-success">
                        👨‍💼 Staff is here
                    </div>
                @endif





                {{-- Input --}}
                <div class="card-footer">

                    <form action="{{ route('customer.chat.message') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Enter your message..."
                                required>
                            <button class="btn btn-primary">
                                Send
                            </button>
                        </div>
                    </form>

                </div>


            </div>

            <div class="position-relative mt-4">

                {{-- Home --}}
                <div class="position-absolute start-0">
                    <a href="{{ url('/') }}" class="btn btn-dark ps-3 pe-3">
                        Home
                    </a>
                </div>
            
                {{-- Chat Reset --}}
                <div class="d-flex justify-content-center">
                    <form action="{{ route('customer.chat.close') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger ps-5 pe-5">
                            Chat Reset
                        </button>
                    </form>
                </div>
            </div>



        </div>
    </div>
@endsection