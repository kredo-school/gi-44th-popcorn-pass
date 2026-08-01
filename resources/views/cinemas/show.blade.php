@extends('layouts.app')

@section('title', $cinema->cinema_name . ' - Cinema Profile')

@section('content')
<div class="container py-5">
    {{-- Cinema Header --}}
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="mb-2">{{ $cinema->cinema_name }}</h1>
            <p class="text-muted mb-3">
                <i class="fa-solid fa-map-pin me-2"></i>
                {{ $cinema->address }}, {{ $cinema->city }}
            </p>

            {{-- Overall Score --}}
            <div class="mb-4">
                <h3 class="mb-3">Overall Experience Score</h3>
                <div class="d-flex align-items-center gap-3">
                    <div class="display-4 fw-bold">
                        {{ number_format($avgScores['overall'], 1) }}/5
                    </div>
                    <div>
                        <div class="mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $avgScores['overall'])
                                    <span class="text-warning fa-solid fa-star"></span>
                                @else
                                    <span class="text-secondary fa-regular fa-star"></span>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">
                            {{ $cinema->total_reviews }} review{{ $cinema->total_reviews != 1 ? 's' : '' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Score Breakdown --}}
    <div class="row mb-5">
        <div class="col-md-12">
            <h3 class="mb-4">Rating Breakdown</h3>

            <div class="row g-4">
                {{-- Image Quality --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-film text-info me-2"></i>Image Quality
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $avgScores['image_quality'])
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['image_quality'], 1) }}/5</small>
                        </div>
                    </div>
                </div>

                {{-- Sound Quality --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-volume-high text-info me-2"></i>Sound Quality
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $avgScores['sound_quality'])
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['sound_quality'], 1) }}/5</small>
                        </div>
                    </div>
                </div>

                {{-- Seat Comfort --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-chair text-info me-2"></i>Seat Comfort
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $avgScores['seat_comfort'])
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['seat_comfort'], 1) }}/5</small>
                        </div>
                    </div>
                </div>

                {{-- Crowding Level --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-people-group text-info me-2"></i>Crowding Level
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= (5 - $avgScores['crowding_level']))
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['crowding_level'], 1) }}/5 (lower is better)</small>
                        </div>
                    </div>
                </div>

                {{-- Accessibility --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-wheelchair text-info me-2"></i>Accessibility
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $avgScores['accessibility'])
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['accessibility'], 1) }}/5</small>
                        </div>
                    </div>
                </div>

                {{-- Service Quality --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-handshake text-info me-2"></i>Service Quality
                            </h6>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $avgScores['service_quality'])
                                        <span class="text-warning fa-solid fa-star"></span>
                                    @else
                                        <span class="text-secondary fa-regular fa-star"></span>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avgScores['service_quality'], 1) }}/5</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    <div class="row">
        <div class="col-md-12">
            <h3 class="mb-4">Reviews</h3>

            @if ($reviews->isEmpty())
                <p class="text-muted">No reviews yet. Be the first to review this cinema!</p>
            @else
                @foreach ($reviews as $review)
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="card-title mb-1">{{ $review->user->name ?? 'Anonymous' }}</h6>
                                    <small class="text-muted">{{ $review->visited_at->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->calculateOverallScore())
                                            <span class="text-warning fa-solid fa-star"></span>
                                        @else
                                            <span class="text-secondary fa-regular fa-star"></span>
                                        @endif
                                    @endfor
                                    <div><small class="text-muted">{{ number_format($review->calculateOverallScore(), 1) }}/5</small></div>
                                </div>
                            </div>

                            @if ($review->comment)
                                <p class="card-text">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-5">
        <a href="javascript:history.back()" class="btn btn-outline-primary">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>
@endsection