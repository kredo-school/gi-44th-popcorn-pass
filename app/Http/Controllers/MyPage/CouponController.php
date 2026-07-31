<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'available');

        if (!in_array($tab, ['available', 'used', 'expired'])) {
            $tab = 'available';
        }

        $couponsQuery = $user->coupons();

        if ($tab === 'available') {
            $couponsQuery
                ->wherePivotNull('used_at')
                ->where('coupon_status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->orderByRaw('expires_at IS NULL, expires_at ASC');
        } elseif ($tab === 'used') {
            $couponsQuery
                ->wherePivotNotNull('used_at')
                ->orderByPivot('used_at', 'desc');
        } elseif ($tab === 'expired') {
            $couponsQuery
                ->wherePivotNull('used_at')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->orderBy('expires_at', 'desc');
        }

        $coupons = $couponsQuery
            ->paginate(10)
            ->withQueryString();

        return view('mypage.coupons.index', compact('coupons', 'tab'));
    }
}
