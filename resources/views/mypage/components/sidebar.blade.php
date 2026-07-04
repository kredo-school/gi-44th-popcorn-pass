{{-- resources/views/mypage/components/sidebar.blade.php --}}
<div class="mypage-sidebar-profile text-center mb-4">
    <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
         alt="{{ $user->full_name }}"
         class="mypage-sidebar-avatar rounded-circle mb-2">

    <div class="mb-1">
        <a href="{{ route('mypage.profile') }}" class="mypage-sidebar-name text-decoration-none text-white">
            {{ $user->full_name }}
        </a>
    </div>

    <span class="mypage-tier-badge mypage-tier-{{ $user->tier }}">
        {{ $user->tier_label }} Member
    </span>
</div>

<div class="mypage-sidebar-stats d-flex flex-column gap-3">
    <a href="{{ route('mypage.tickets') }}" class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-ticket me-2"></i>Upcoming Tickets</span>
        <strong>{{ $upcomingTicketsCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.movies-watched') }}" class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-film me-2"></i>Movies Watched</span>
        <strong>{{ $moviesWatchedCount ?? 0 }}</strong>
    </a>

    <a href="{{ route('mypage.reviews-written') }}" class="mypage-stat-item d-flex align-items-center justify-content-between text-decoration-none">
        <span><i class="fa-solid fa-star me-2"></i>Reviews Written</span>
        <strong>{{ $reviewsWrittenCount ?? 0 }}</strong>
    </a>
</div>