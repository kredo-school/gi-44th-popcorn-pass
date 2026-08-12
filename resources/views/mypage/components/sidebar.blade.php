@php
    $user = auth()->user();

    $upcomingTicketsCount = $user
        ->reservations()
        ->where('reservation_status', 'confirmed')
        ->whereHas('showtime', function ($query) {
            $query->where('start_time', '>', now());
        })
        ->count();

    $moviesWatchedCount = $user
        ->reservations()
        ->where('reservation_status', 'confirmed')
        ->whereHas('showtime', function ($query) {
            $query->where('start_time', '<=', now());
        })
        ->count();

    $reviewsWrittenCount = $user->reviews()->count();

    $couponsCount = $user
        ->coupons()
        ->wherePivotNull('used_at')
        ->where('coupon_status', 'active')
        ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        })
        ->count();
@endphp

<a href="{{ route('mypage.profile') }}" class="d-block text-decoration-none text-white">

    <div class="mypage-sidebar-profile text-center mb-4">
        <img src="{{ $user->avatar ?? asset('images/profile.png') }}" alt="{{ $user->full_name }}"
            class="mypage-sidebar-avatar rounded-circle mb-2">

        <div class="mb-1">
            <span class="mypage-sidebar-name">
                {{ $user->full_name }}
            </span>
        </div>

        <span class="mypage-tier-badge mypage-tier-{{ $user->tier }}">
            {{ $user->tier_label }} Member
        </span>
    </div>

</a>

<div class="mypage-sidebar-stats d-flex flex-column gap-3">
    <a href="{{ route('mypage.tickets') }}"
        class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-ticket me-2"></i>Upcoming Tickets</span>
        <strong>{{ $upcomingTicketsCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.movies-watched') }}"
        class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-film me-2"></i>Movies Watched</span>
        <strong>{{ $moviesWatchedCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.reviews-written') }}"
        class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-star me-2"></i>Reviews Written</span>
        <strong>{{ $reviewsWrittenCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.coupons') }}"
        class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-tags me-2"></i>Coupons</span>
        <strong>{{ $couponsCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.profile') }}"
        class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none mt-3 border">
        <span>
            <i class="fa-solid fa-user me-2"></i>Edit Profile
        </span>
    </a>

</div>
