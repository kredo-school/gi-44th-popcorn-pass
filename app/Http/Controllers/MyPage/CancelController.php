<?php
// app/Http/Controllers/MyPage/CancelController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CancelController extends Controller
{
    private function sidebarCounts($user): array
    {
        return [
            'upcomingTicketsCount' => $user->reservations()
                ->where('reservation_status', 'confirmed')
                ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
                ->count(),
            'moviesWatchedCount' => $user->reservations()
                ->where('reservation_status', 'confirmed')
                ->whereHas('showtime', fn ($q) => $q->where('start_time', '<=', now()))
                ->count(),
            'reviewsWrittenCount' => $user->reviews()->count(),
        ];
    }

    public function show(string $id): View
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with(['movie', 'showtime', 'screen'])
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->where('reservation_status', 'confirmed')
            ->findOrFail($id);

        return view('mypage.cancel.show', array_merge(
            ['user' => $user, 'reservation' => $reservation],
            $this->sidebarCounts($user)
        ));
    }

    public function cancel(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->whereHas('showtime', fn ($q) => $q->where('start_time', '>', now()))
            ->where('reservation_status', 'confirmed')
            ->findOrFail($id);

        $reservation->update([
            'reservation_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('mypage.cancel.complete', $id);
    }

    public function complete(string $id): View
    {
        $user = Auth::user();

        $reservation = $user->reservations()
            ->with(['movie', 'showtime'])
            ->where('reservation_status', 'cancelled')
            ->findOrFail($id);

        return view('mypage.cancel.complete', array_merge(
            ['user' => $user, 'reservation' => $reservation],
            $this->sidebarCounts($user)
        ));
    }
}