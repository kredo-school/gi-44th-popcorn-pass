<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The form field name used for throttle keys etc.
     * The actual column (username/email/phone) is resolved in attemptLogin().
     */
    public function username(): string
    {
        return 'identifier';
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
    }

    protected function attemptLogin(Request $request): bool
    {
        $field = $this->resolveLoginField(
            $request->input('identifier')
        );

        $success = $this->guard()->attempt([
            $field => $request->input('identifier'),
            'password' => $request->input('password'),
        ], $request->boolean('remember'));

        // Login successful → update last login time
        if ($success) {
            $user = $this->guard()->user();

            $user->update([
                'last_login_at' => now(),
            ]);
        }

        return $success;
    }

    /**
     * Decide whether the "identifier" input is a username, email, or phone.
     */
    protected function resolveLoginField(string $identifier): string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (preg_match('/^[0-9\-+() ]+$/', $identifier)) {
            return 'phone';
        }

        return 'username';
    }

    protected function sendFailedLoginResponse(Request $request): void
    {
        throw ValidationException::withMessages([
            'identifier' => [trans('auth.failed')],
        ]);
    }

    /**
     * Role-aware redirect after a successful login.
     */
    protected function redirectTo(): string
    {
        $user = Auth::user();

        if ($user && $user->isAdminPanelUser()) {
            return route('admin.dashboard');
        }

        if (session()->has('url.intended')) {
            return session()->pull('url.intended');
        }

        return route('mypage.dashboard');
    }
}