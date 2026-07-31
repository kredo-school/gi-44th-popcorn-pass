@extends('layouts.mypage')

@section('title', 'My Coupons')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fa-solid fa-tags me-2"></i>

            @if ($tab === 'available')
                Available Coupons
            @elseif ($tab === 'used')
                Used Coupons
            @elseif ($tab === 'expired')
                Expired Coupons
            @endif
        </h2>

        <div class="mypage-tab-switch">
            <a href="{{ route('mypage.coupons', ['tab' => 'available']) }}"
                class="mypage-tab {{ $tab === 'available' ? 'is-active' : '' }}">
                Available
            </a>

            <a href="{{ route('mypage.coupons', ['tab' => 'used']) }}"
                class="mypage-tab {{ $tab === 'used' ? 'is-active' : '' }}">
                Used
            </a>

            <a href="{{ route('mypage.coupons', ['tab' => 'expired']) }}"
                class="mypage-tab {{ $tab === 'expired' ? 'is-active' : '' }}">
                Expired
            </a>
        </div>
    </div>

    <div class="mypage-card p-4">
        @if ($coupons->isEmpty())
            <p class="text-muted mb-0">
                @if ($tab === 'available')
                    You don't have any available coupons.
                @elseif ($tab === 'used')
                    You don't have any used coupons.
                @elseif ($tab === 'expired')
                    You don't have any expired coupons.
                @endif
            </p>
        @else
            @foreach ($coupons as $coupon)
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">
                            {{ $coupon->code }}
                
                            @if ($tab === 'available')
                                <span class="badge bg-success ms-2">Available</span>
                            @elseif ($tab === 'used')
                                <span class="badge bg-secondary ms-2">Used</span>
                            @else
                                <span class="badge bg-danger ms-2">Expired</span>
                            @endif
                        </div>
                
                        <div class="small text-muted mt-1">
                            @if ($coupon->coupon_type === 'percentage')
                                {{ $coupon->discount_percent }}% OFF
                            @else
                                ${{ number_format($coupon->discount_amount, 2) }} OFF
                            @endif
                        </div>
                
                        <div class="small text-muted mt-1">
                            @if ($tab === 'used' && $coupon->pivot->used_at)
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Used: {{ \Carbon\Carbon::parse($coupon->pivot->used_at)->format('M d, Y') }}
                            @elseif ($coupon->expires_at)
                                <i class="fa-solid fa-calendar me-1"></i>
                                Expires: {{ $coupon->expires_at->format('M d, Y') }}
                            @else
                                <i class="fa-solid fa-infinity me-1"></i>
                                No expiration date
                            @endif
                        </div>
                    </div>
                </div>
            
                @unless ($loop->last)
                    <hr class="my-0 border-secondary">
                @endunless
            @endforeach
        @endif
    </div>

    @if ($coupons->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $coupons->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>


@endsection