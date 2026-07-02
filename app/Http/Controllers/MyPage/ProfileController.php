<?php
// app/Http/Controllers/MyPage/ProfileController.php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
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

    public function show(): View
    {
        $user = Auth::user();

        return view('mypage.profile.show', array_merge(
            ['user' => $user],
            $this->sidebarCounts($user)
        ));
    }

    public function edit(): View
    {
        $user = Auth::user();

        return view('mypage.profile.edit', array_merge(
            ['user' => $user],
            $this->sidebarCounts($user)
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'gender' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->update($data);

        return redirect()->route('mypage.profile')
            ->with('success', 'Your profile has been updated.');
    }
}