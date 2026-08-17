@extends('layouts.admin')

@section('title', 'Admin Chat')
@section('page-title', 'Customer Chat')

@section('content')

    <div class="admin-chat-list-page">

        {{-- Page Header --}}
        <div class="admin-chat-header">

            <div>
                <div class="admin-chat-subtitle">
                    Customer Support
                </div>

                <h1>
                    Customer Chat
                </h1>
            </div>

            <div class="admin-chat-count">
                <span>Support Requests</span>
                <strong>{{ $chatNotificationCount }}</strong>
            </div>

        </div>


        {{-- Main Card --}}
        <div class="admin-chat-list-card">

            <div class="admin-chat-list-header">

                <div>
                    <h2>
                        👨‍💻 Customer Support Requests
                    </h2>

                    <p>
                        Manage customer conversations and support requests
                    </p>
                </div>

                <div class="admin-chat-total">
                    {{ $conversations->total() }} Requests
                </div>

            </div>


            {{-- Conversation List --}}
            <div class="admin-chat-list">

                @forelse($conversations as $conversation)
                    <div class="admin-chat-item">

                        {{-- User --}}
                        <div class="admin-chat-user">

                            <div class="admin-chat-avatar">
                                👤
                            </div>

                            <div>

                                <h5>
                                    {{ $conversation->user->username }}
                                </h5>

                                <span>
                                    Customer
                                </span>

                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="admin-chat-status">

                            <span class="status-label">
                                Status
                            </span>

                            @if ($conversation->status === 'waiting')
                                <span class="chat-status waiting">
                                    Waiting
                                </span>
                            @elseif($conversation->status === 'staff')
                                <span class="chat-status staff">
                                    Staff
                                </span>
                            @elseif($conversation->status === 'closed')
                                <span class="chat-status closed">
                                    Closed
                                </span>
                            @else
                                <span class="chat-status">
                                    {{ ucfirst($conversation->status) }}
                                </span>
                            @endif

                        </div>


                        {{-- Open --}}
                        <div class="admin-chat-action">

                            <a href="{{ route('admin.chat.show', $conversation->id) }}" class="admin-chat-open-btn">
                                Open Chat
                                <span>→</span>
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="admin-chat-empty">

                        <div class="empty-icon">
                            💬
                        </div>

                        <h3>
                            No support requests
                        </h3>

                        <p>
                            There are currently no customer support requests.
                        </p>

                    </div>
                @endforelse

            </div>


            {{-- Pagination --}}
            @if ($conversations->hasPages())
                <div class="admin-chat-pagination">

                    {{ $conversations->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
