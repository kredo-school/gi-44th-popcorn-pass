<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = auth()->user()
            ->coupons()
            ->withPivot('used_at')
            ->latest()
            ->paginate(10);

        return view('mypage.coupons.index', compact('coupons'));
    }
}
