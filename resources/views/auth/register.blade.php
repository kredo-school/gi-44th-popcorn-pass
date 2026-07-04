{{-- resources/views/auth/register.blade.php --}}
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
        max-width: 620px;
        background: rgba(26, 31, 54, 0.88);
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 18px;
        padding: 36px 34px 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
    }

    .pp-auth-tab {
        display: block;
        width: fit-content;
        margin: -66px auto 24px;
        background: #2f3a66;
        color: #ffd700;
        font-weight: 700;
        letter-spacing: 0.03em;
        padding: 10px 32px;
        border-radius: 10px;
        border: 1px solid rgba(255, 215, 0, 0.5);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }

    .pp-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 16px;
    }

    .pp-form-grid.pp-cols-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .pp-field {
        margin-bottom: 16px;
    }

    .pp-field.pp-span-2 {
        grid-column: 1 / -1;
    }

    .pp-field label {
        display: block;
        color: #ffd700;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .pp-field input,
    .pp-field select {
        width: 100%;
        background: #10152b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border-radius: 8px;
        padding: 11px 13px;
        font-size: 0.92rem;
    }

    .pp-field input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .pp-field input:focus,
    .pp-field select:focus {
        outline: none;
        border-color: #ffd700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }

    .pp-field select option {
        background: #10152b;
        color: #ffffff;
    }

    .pp-field .invalid-feedback {
        display: block;
        color: #ff8a8a;
        font-size: 0.78rem;
        margin-top: 4px;
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
        margin-top: 6px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .pp-btn-gold:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(255, 215, 0, 0.35);
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

    @media (max-width: 576px) {
        .pp-form-grid, .pp-form-grid.pp-cols-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pp-auth-hero">
    <div class="pp-auth-card">
        <span class="pp-auth-tab">{{ __('Registration') }}</span>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="pp-field">
                <label for="username">{{ __('Username') }}</label>
                <input id="username" type="text" name="username"
                    class="@error('username') is-invalid @enderror" value="{{ old('username') }}"
                    required autocomplete="username" autofocus>
                @error('username')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="pp-form-grid">
                <div class="pp-field">
                    <label for="last_name">{{ __('Last Name') }}</label>
                    <input id="last_name" type="text" name="last_name"
                        class="@error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="first_name">{{ __('First Name') }}</label>
                    <input id="first_name" type="text" name="first_name"
                        class="@error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="pp-field">
                <label for="email">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email"
                    class="@error('email') is-invalid @enderror" value="{{ old('email') }}" required
                    autocomplete="email">
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="pp-field">
                <label for="phone">{{ __('Phone Number') }}</label>
                <input id="phone" type="text" name="phone"
                    class="@error('phone') is-invalid @enderror" value="{{ old('phone') }}" required
                    autocomplete="tel">
                @error('phone')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="pp-form-grid">
                <div class="pp-field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password"
                        class="@error('password') is-invalid @enderror" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required
                        autocomplete="new-password">
                </div>
            </div>

            <div class="pp-form-grid pp-cols-3">
                <div class="pp-field">
                    <label for="age">{{ __('Age') }}</label>
                    <input id="age" type="number" min="1" max="120" name="age"
                        class="@error('age') is-invalid @enderror" value="{{ old('age') }}">
                    @error('age')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="gender">{{ __('Gender') }}</label>
                    <select id="gender" name="gender" class="@error('gender') is-invalid @enderror">
                        <option value="" selected disabled>{{ __('Select') }}</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                    </select>
                    @error('gender')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="occupation">{{ __('Occupation') }}</label>
                    <input id="occupation" type="text" name="occupation"
                        class="@error('occupation') is-invalid @enderror" value="{{ old('occupation') }}">
                    @error('occupation')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="pp-btn-gold">{{ __('Register') }}</button>
        </form>

        <img src="{{ asset('images/popcorn-mascot.png') }}" class="pp-mascot" alt="Popcorn Pass mascot"
            onerror="this.style.display='none'">
    </div>
</div>
@endsection