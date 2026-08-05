@extends('layouts.app')

@section('title', 'Chat')

@section('content')

    <div class="container py-5">

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
        <div class="d-flex align-items-center position-relative mt-3">

            <a class="btn btn-dark ms-1" href="{{ url('/') }}">
                Home
            </a>

            <form action="{{ route('customer.chat.close') }}" method="POST"
                class="position-absolute start-50 translate-middle-x">
                @csrf
                <button type="submit" class="btn btn-danger " style="width: 400px;">
                    Reset
                </button>
            </form>

        </div>


    </div>

    @vite('resources/js/customer/chat.js')

@endsection
