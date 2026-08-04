@extends('layouts.admin')

@section('title', 'Admin Chat')
@section('page-title', 'Customer Chat')
@section('content')



    <div class="container py-5">


        <h2 class="mb-4 text-white ">
            👨‍💻 Customer Support Requests
        </h2>



        <div class="chat-display-bg p-3">
            <div class="card shadow w-50 mx-auto">

                <div class="card-body">

                    @forelse($conversations as $conversation)
                        <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center">

                            <div>
                                <h5>
                                    👤
                                    {{ $conversation->user->first_name }} {{ $conversation->user->last_name }}
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


                            <div>
                                <a href="{{ route('admin.chat.show', $conversation->id) }}" class="text-dark btn border-dark">
                                    Open Chat
                                </a>
                            </div>

                        </div>



                    @empty


                        <div class="text-center text-secondary">

                            No support requests.

                        </div>
                    @endforelse

                    {{ $conversations->links() }}

                </div>

            </div>
        </div>



    </div>




@endsection
