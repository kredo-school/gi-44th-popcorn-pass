{{-- resources/views/mypage/profile/show.blade.php --}}
@extends('layouts.mypage')

@section('title', 'My Profile')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-user me-2"></i>Profile</h2>
    </div>

    <div class="mypage-card p-4">
        {{-- Avatar + Name + Tier --}}
        <div class="text-center mb-4 pb-4" style="border-bottom: 1px solid #2a2f4a;">
            <div class="mypage-profile-avatar-wrapper mx-auto mb-3">
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}"
                         alt="{{ $user->full_name }}"
                         class="mypage-profile-avatar rounded-circle">
                @else
                    <div class="mypage-profile-avatar mypage-profile-avatar-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user fa-3x"></i>
                    </div>
                @endif
            </div>

            <h4 class="mb-1">{{ $user->full_name }}</h4>
            <span class="mypage-tier-badge mypage-tier-{{ $user->tier }}">
                {{ $user->tier_label }} Member
            </span>
        </div>

        {{-- Profile Info --}}
        <div class="mypage-profile-info">
            <div class="mypage-profile-row py-3">
                <div class="small text-muted mb-1">Name</div>
                <div class="fw-bold">{{ $user->full_name ?: '—' }}</div>
            </div>

            <div class="mypage-profile-row py-3">
                <div class="small text-muted mb-1">Email</div>
                <div class="fw-bold">{{ $user->email }}</div>
            </div>

            <div class="mypage-profile-row py-3">
                <div class="small text-muted mb-1">Phone</div>
                <div class="fw-bold">{{ $user->phone ?: '—' }}</div>
            </div>

            @if ($user->date_of_birth)
                <div class="mypage-profile-row py-3">
                    <div class="small text-muted mb-1">Age</div>
                    <div class="fw-bold">{{ $user->age }} years old</div>
                </div>
            @endif

            @if ($user->gender)
                <div class="mypage-profile-row py-3">
                    <div class="small text-muted mb-1">Gender</div>
                    <div class="fw-bold">{{ ucfirst($user->gender) }}</div>
                </div>
            @endif

            @if ($user->occupation)
                <div class="mypage-profile-row py-3">
                    <div class="small text-muted mb-1">Occupation</div>
                    <div class="fw-bold">{{ $user->occupation }}</div>
                </div>
            @endif
        </div>

        {{-- Edit Profile Button (COMPLETE項目、今はリンクのみ設置) --}}
        <div class="mt-4 text-center">
            <a href="{{ route('mypage.profile.edit') }}" class="btn mypage-btn-write-review px-4">
                <i class="fa-solid fa-pen me-2"></i>Edit Profile
            </a>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn mypage-btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection

