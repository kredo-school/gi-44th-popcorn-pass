@extends('layouts.mypage')
@section('title', 'My Coupons')
@section('content')

    <div class="mypage-page-title mb-4">
        <h2>
            <i class="fa-solid fa-tags me-2"></i>My Coupons
        </h2>
    </div>


    <div class="mypage-card p-4">
        @if ($coupons->isEmpty())
            <p class="text-muted mb-0">
                You don't have any coupons yet.
            </p>
        @else
            <div class="row g-4">
                @foreach ($coupons as $coupon)
                    <div class="col-md-6">
                        <div class="coupon-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        {{ $coupon->code }}
                                    </h5>
                                    @if ($coupon->coupon_type === 'percentage')
                                        <div class="coupon-discount">
                                            {{ $coupon->discount_percent }}% OFF
                                        </div>
                                    @else
                                        <div class="coupon-discount">
                                            ${{ number_format($coupon->discount_amount, 2) }} OFF
                                        </div>
                                    @endif
                                </div>

                                @if ($coupon->pivot->used_at)
                                    <span class="badge bg-secondary">
                                        Used
                                    </span>
                                @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                                    <span class="badge bg-danger">
                                        Expired
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        Available
                                    </span>
                                @endif
                            </div>

                            <div class="small text-muted">
                                @if ($coupon->expires_at)
                                    <i class="fa-solid fa-calendar me-1"></i>
                                    Expires:
                                    {{ $coupon->expires_at->format('M d, Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="mt-4">
                {{ $coupons->links() }}
            </div>

        @endif

    </div>

    <div class="mt-4">
        <a href="{{ route('mypage.dashboard') }}" class="btn border-white text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

@endsection