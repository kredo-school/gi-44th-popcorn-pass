{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    .pp-auth-hero {
        position: relative;
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        overflow: hidden;
        background:
            radial-gradient(circle at 20% 20%, rgba(255, 215, 0, 0.15), transparent 40%),
            radial-gradient(circle at 80% 15%, rgba(255, 215, 0, 0.12), transparent 35%),
            linear-gradient(180deg, #0d0f1a 0%, #1a1f36 55%, #2a0f14 100%);
        background-image: url('{{ asset('images/auth-bg.jpg') }}'), radial-gradient(circle at 20% 20%, rgba(255, 215, 0, 0.15), transparent 40%), radial-gradient(circle at 80% 15%, rgba(255, 215, 0, 0.12), transparent 35%), linear-gradient(180deg, #0d0f1a 0%, #1a1f36 55%, #2a0f14 100%);
        background-size: cover;
        background-position: center;
    }

    .pp-auth-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(230, 57, 70, 0.35) 0%, transparent 12%, transparent 88%, rgba(230, 57, 70, 0.35) 100%);
        pointer-events: none;
    }

    .pp-auth-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 440px;
        background: rgba(26, 31, 54, 0.88);
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 18px;
        padding: 36px 34px 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
    }

    .pp-auth-tab {
        display: inline-block;
        margin: -66px auto 24px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        left: 50%;
        transform: translateX(-50%);
        background: #2f3a66;
        color: #ffd700;
        font-weight: 700;
        letter-spacing: 0.03em;
        padding: 10px 32px;
        border-radius: 10px;
        border: 1px solid rgba(255, 215, 0, 0.5);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }

    .pp-field {
        margin-bottom: 18px;
    }

    .pp-field label {
        display: block;
        color: #ffd700;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .pp-field input {
        width: 100%;
        background: #10152b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 0.95rem;
    }

    .pp-field input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .pp-field input:focus {
        outline: none;
        border-color: #ffd700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }

    .pp-field .invalid-feedback {
        display: block;
        color: #ff8a8a;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .pp-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 22px;
    }

    .pp-remember label {
        color: #d9dcea;
        font-size: 0.85rem;
        margin: 0;
    }

    .pp-btn-gold {
        width: 100%;
        background: #ffd700;
        color: #1a1f36;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-size: 1rem;
        letter-spacing: 0.02em;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .pp-btn-gold:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(255, 215, 0, 0.35);
    }

    .pp-auth-links {
        text-align: center;
        margin-top: 16px;
    }

    .pp-auth-links a {
        color: #d9dcea;
        font-size: 0.82rem;
        text-decoration: underline;
    }

    .pp-mascot {
        position: absolute;
        right: -18px;
        bottom: -26px;
        width: 110px;
        height: auto;
        pointer-events: none;
        filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.5));
    }
</style>

<div class="pp-auth-hero">
    <div class="pp-auth-card">
        <span class="pp-auth-tab">{{ __('Login') }}</span>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="pp-field">
                <label for="identifier">{{ __('Email / Username / Phone') }}</label>
                <input id="identifier" type="text" name="identifier"
                    class="@error('identifier') is-invalid @enderror" value="{{ old('identifier') }}"
                    required autocomplete="username" autofocus>
                @error('identifier')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="pp-field">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password" name="password"
                    class="@error('password') is-invalid @enderror" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="pp-remember">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">{{ __('Remember Me') }}</label>
            </div>

            <button type="submit" class="pp-btn-gold">{{ __('Login') }}</button>

            <div class="pp-auth-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">{{ __('Forgot your email or password? Click here.') }}</a>
                @endif
            </div>
        </form>

        <img src="{{ asset('images/popcorn-mascot.png') }}" class="pp-mascot" alt="Popcorn Pass mascot"
            onerror="this.style.display='none'">
    </div>
</div>
@endsection