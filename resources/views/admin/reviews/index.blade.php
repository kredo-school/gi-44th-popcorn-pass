@extends('layouts.admin')

@section('title', 'Review Management')
@section('page-title', 'Review Management')

@section('content')

{{-- Filter --}}
<form method="GET" action="{{ route('admin.reviews') }}" class="d-flex gap-2 mb-3">

    <input type="text" name="search" class="form-control" placeholder="Search by movie or username..."
        style="max-width: 250px;" value="{{ request('search') }}">

    <select name="status" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
        <option value="all" {{ request('status', 'visible' )=='all' ? 'selected' : '' }}>Status: All</option>
        <option value="visible" {{ request('status', 'visible' )=='visible' ? 'selected' : '' }}>Visible</option>
        <option value="hidden" {{ request('status', 'visible' )=='hidden' ? 'selected' : '' }}>Hidden</option>
    </select>

    <select name="sort" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
        <option value="desc" {{ request('sort', 'desc' )=='desc' ? 'selected' : '' }}>Newest</option>
        <option value="asc" {{ request('sort', 'desc' )=='asc' ? 'selected' : '' }}>Oldest</option>
    </select>

    <button type="submit" class="btn btn-outline-warning">Search</button>

</form>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card card-dark p-3">
    <table class="table table-dark table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Movie</th>
                <th>User</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr>
                <td>{{ $review->movie->title ?? '—' }}</td>
                <td>{{ $review->user->username ?? '—' }}</td>
                <td>{{ $review->rating }}/5</td>
                <td>{{ Str::limit($review->body, 80) }}</td>
                <td>
                    @if($review->is_approved)
                    <span class="badge bg-success">Visible</span>
                    @else
                    <span class="badge bg-danger">Hidden</span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.reviews.toggle', $review->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="btn btn-sm {{ $review->is_approved ? 'btn-danger' : 'btn-success' }}">
                            {{ $review->is_approved ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-secondary py-4">No reviews found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $reviews->links() }}
</div>

@endsection