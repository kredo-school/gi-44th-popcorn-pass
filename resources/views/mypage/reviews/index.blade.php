@extends('layouts.mypage')

@section('title', 'Reviews Written')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-star me-2"></i>Reviews Written</h2>

        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="sort" class="small text-muted mb-0">Sort by:</label>
            <select id="sort" name="sort" class="form-select form-select-sm mypage-sort-select" onchange="this.form.submit()">
                <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>Most Recent</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
            </select>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mypage-card p-4">
        @if ($reviews->isEmpty())
            <p class="text-muted mb-0">You haven't written any reviews yet.</p>
        @else
            @foreach ($reviews as $review)
                <div class="mypage-watched-row d-flex justify-content-between align-items-start py-3">
                    <div class="d-flex gap-3 align-items-start">
                        @if ($review->movie->poster_url)
                            <img src="{{ $review->movie->poster_url }}"
                                 alt="{{ $review->movie->title }}"
                                 class="mypage-watched-row-poster">
                        @else
                            <div class="mypage-watched-row-poster mypage-poster-placeholder">
                                <i class="fa-solid fa-film"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold mb-1">{{ $review->movie->title }}</div>
                            <div class="mypage-stars mb-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </div>
                            <p class="small text-muted mb-1">{{ Str::limit($review->body, 120) }}</p>
                            <div class="small text-muted">
                                <i class="fa-solid fa-calendar me-1"></i>
                                {{ $review->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="ms-3 flex-shrink-0">
                        <a href="{{ route('reviews.edit', [
                                'movieId' => $review->movie_id,
                                'reviewId' => $review->id,
                            ]) }}" class="btn border-warning text-warning btn-sm">
                            <i class="fa-solid fa-pen me-1"></i>
                            Edit Review
                        </a>

                        {{-- Edit Review Modal --}}
                        <div class="modal fade" id="editReviewModal-{{ $review->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content mypage-modal">
                                    <form method="POST" action="{{ route('mypage.reviews.update', $review->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Review: {{ $review->movie->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Your Rating</label>
                                            <div class="mypage-star-input mb-3">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <input type="radio" name="rating"
                                                           id="editStar{{ $i }}-{{ $review->id }}"
                                                           value="{{ $i }}"
                                                           {{ $review->rating === $i ? 'checked' : '' }}>
                                                    <label for="editStar{{ $i }}-{{ $review->id }}">
                                                        <i class="fa-solid fa-star"></i>
                                                    </label>
                                                @endfor
                                            </div>

                                            <label class="form-label">Your Review</label>
                                            <textarea name="body" class="form-control" rows="4"
                                                      required maxlength="2000">{{ $review->body }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn border-danger text-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn border-dark text-dark">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if ($reviews->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection