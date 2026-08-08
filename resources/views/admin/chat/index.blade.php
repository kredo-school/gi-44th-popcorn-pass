@extends('layouts.admin')

@section('title', 'Admin Chat')
@section('page-title', 'Customer Chat')
@section('content')



    <div class="container ">

        <div class="chat-display-bg">

            <div class="card shadow admin-chat-list-card mx-auto">
                <h2 class="text-center mt-3">
                    👨‍💻 Customer Support Requests <span class="text-danger">({{ $chatNotificationCount }})</span>
                </h2>

                

                <div class="card-body">

                    @forelse($conversations as $conversation)
                        <div class="border rounded p-1 mb-3 d-flex justify-content-between align-items-center">

                            <div>
                                <h5>
                                    👤 {{ $conversation->user->username }}
                                </h5>

                                <p class="mb-0">
                                    Status:

                                    @if ($conversation->status === 'waiting')
                                        <span class="badge bg-danger">
                                            Waiting
                                        </span>
                                    @elseif($conversation->status === 'staff')
                                        <span class="badge bg-primary">
                                            Staff
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($conversation->status) }}
                                        </span>
                                    @endif

                                </p>
                            </div>
                            <a href="{{ route('admin.chat.show', $conversation->id) }}" class="btn btn-outline-dark">
                                Open Chat
                            </a>
                        </div>
                    @empty

                        <div class="text-center text-secondary">
                            No support requests.
                        </div>
                    @endforelse


                </div>

                {{-- pagination --}}
                <div class="d-flex justify-content-center ">

                    {{ $conversations->links() }}

                </div>

            </div>

        </div>



    </div>




@endsection
