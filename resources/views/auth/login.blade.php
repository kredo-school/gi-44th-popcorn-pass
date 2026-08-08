@extends('layouts.app')

@section('content')
    
    <div class="pp-auth-hero">
        <div class="pp-auth-card pp-auth-card--sm">
            <span class="pp-auth-tab">{{ __('Login') }}</span>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="pp-field">
                    <label for="identifier">{{ __('Email / Username / Phone') }}</label>
                    <input id="identifier" type="text" name="identifier" class="@error('identifier') is-invalid @enderror"
                        value="{{ old('identifier') }}" required autocomplete="username" autofocus>
                    @error('identifier')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" class="@error('password') is-invalid @enderror"
                        required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="row">
                    <div class="pp-remember col-6">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">{{ __('Remember Me') }}</label>
                    </div>

                    <div class="col-6">
                        <a href="/register">Register a new account</a>
                    </div>
                </div>


                <button type="submit" class="pp-btn-gold">{{ __('Login') }}</button>

                <div class="pp-auth-links">
                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}">{{ __('Forgot your email or password? Click here.') }}</a>
                    @endif
                </div>
            </form>

            <img src="{{ asset('images/popcorn-mascot.png') }}" class="pp-mascot" alt="Popcorn Pass mascot"
                onerror="this.style.display='none'">
        </div>
    </div>
@endsection
