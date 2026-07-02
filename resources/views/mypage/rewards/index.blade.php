{{-- resources/views/mypage/rewards/index.blade.php --}}
@extends('layouts.mypage')

@section('title', 'Rewards Dashboard')

@section('content')
    <div class="mb-4">
        <h2><i class="fa-solid fa-gem me-2"></i>Rewards Dashboard</h2>
    </div>

    {{-- Current Tier / Current Points --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="mypage-card p-4">
                <div class="small text-muted mb-1">Current Tier</div>
                <div class="mypage-rewards-tier-value mypage-tier-text-{{ $user->tier }}">
                    <i class="fa-solid fa-gem me-2"></i>{{ $user->tier_label }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mypage-card p-4">
                <div class="small text-muted mb-1">Current Points</div>
                <div class="mypage-rewards-points-value">{{ number_format($user->points) }}</div>
                <div class="small text-muted">points available</div>
            </div>
        </div>
    </div>

    {{-- Membership Journey --}}
    <div class="mypage-card p-4 mb-4">
        <h5 class="mb-4">Membership Journey</h5>

        <div class="mypage-journey">
            @foreach ($tiers as $index => $tierKey)
                @php
                    $currentIndex = array_search($user->tier, $tiers);
                    $isReached = $index <= $currentIndex;
                    $isCurrent = $tierKey === $user->tier;
                @endphp
                <div class="mypage-journey-step {{ $isReached ? 'is-reached' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                    <div class="mypage-journey-dot"></div>
                    <div class="mypage-journey-label">{{ ucfirst($tierKey) }}</div>
                </div>
            @endforeach
        </div>

        @php
            $segments = count($tiers) - 1;
            $currentIndex = array_search($user->tier, $tiers);
            $overallProgress = $segments > 0
                ? (($currentIndex + ($user->tier_progress / 100)) / $segments) * 100
                : 100;
        @endphp
        <div class="mypage-journey-bar mt-2">
            <div class="mypage-journey-bar-fill" style="width: {{ min(100, $overallProgress) }}%"></div>
        </div>
    </div>

    {{-- 4 info cards (dummy content — placeholder until point_transactions exists) --}}
    <div class="row g-3">
        <div class="col-md-6 col-lg-3">
            <div class="mypage-card p-4 h-100">
                <h6 class="mb-3"><i class="fa-solid fa-trophy me-2"></i>Rewards History</h6>
                <ul class="mypage-rewards-list">
                    <li>🍿 Free Large Popcorn</li>
                    <li>🎬 2x Movie Passes</li>
                    <li>⭐ VIP Lounge Access</li>
                    <li>🎂 Birthday Combo</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="mypage-card p-4 h-100">
                <h6 class="mb-3"><i class="fa-solid fa-coins me-2"></i>Recent Points Earned</h6>
                <ul class="mypage-rewards-list">
                    <li>+50 Ticket Purchase</li>
                    <li>+30 Snack Combo</li>
                    <li>+100 Referral Bonus</li>
                    <li>+20 Review Posted</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="mypage-card p-4 h-100">
                <h6 class="mb-3"><i class="fa-solid fa-star me-2"></i>Membership Benefits</h6>
                <ul class="mypage-rewards-list">
                    <li>★ Priority Seating</li>
                    <li>★ 20% Off Snacks</li>
                    <li>★ Early Access</li>
                    <li>★ Free Upgrades</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="mypage-card p-4 h-100">
                <h6 class="mb-3"><i class="fa-solid fa-gift me-2"></i>Upcoming Rewards</h6>
                <ul class="mypage-rewards-list">
                    <li>700 pts &mdash; Free Ticket</li>
                    <li>800 pts &mdash; Date Night Pack</li>
                    <li>1000 pts &mdash; Annual Pass</li>
                    <li>1200 pts &mdash; Premiere Invite</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection