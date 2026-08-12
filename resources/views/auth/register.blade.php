@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">


    <div class="pp-auth-hero">
        <div class="pp-auth-card pp-auth-card--lg">
            <span class="pp-auth-tab">{{ __('Registration') }}</span>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="pp-field">
                    <label for="username">{{ __('Username') }}</label>
                    <input id="username" type="text" name="username" class="@error('username') is-invalid @enderror"
                        value="{{ old('username') }}" required autocomplete="username" autofocus>
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
                    <input id="email" type="email" name="email" class="@error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="pp-field">
                    <label for="phone">{{ __('Phone Number') }}</label>
                    <input id="phone" type="text" name="phone" class="@error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}" required autocomplete="tel">
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
                        <label for="date_of_birth">{{ __('Birthday') }}</label>

                        <input id="date_of_birth" type="date" name="date_of_birth"
                            class="@error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">

                        @error('date_of_birth')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="pp-field">
                        <label for="gender">{{ __('Gender') }}</label>
                        <select id="gender" name="gender" class="@error('gender') is-invalid @enderror">
                            <option value="" selected disabled>{{ __('Select') }}</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('Male') }}
                            </option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('Female') }}
                            </option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>{{ __('Other') }}
                            </option>
                        </select>
                        @error('gender')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="pp-field">
                        <label for="occupation">{{ __('Occupation') }}</label>

                        <select id="occupation" name="occupation" class="@error('occupation') is-invalid @enderror">
                            <option value="">Select</option>

                            <option value="student" {{ old('occupation') == 'student' ? 'selected' : '' }}>
                                Student
                            </option>

                            <option value="company_employee"
                                {{ old('occupation') == 'company_employee' ? 'selected' : '' }}>
                                Company Employee
                            </option>

                            <option value="government_employee"
                                {{ old('occupation') == 'government_employee' ? 'selected' : '' }}>
                                Government Employee
                            </option>

                            <option value="self_employed" {{ old('occupation') == 'self_employed' ? 'selected' : '' }}>
                                Self-employed
                            </option>

                            <option value="freelancer" {{ old('occupation') == 'freelancer' ? 'selected' : '' }}>
                                Freelancer
                            </option>

                            <option value="part_time" {{ old('occupation') == 'part_time' ? 'selected' : '' }}>
                                Part-time Worker
                            </option>

                            <option value="homemaker" {{ old('occupation') == 'homemaker' ? 'selected' : '' }}>
                                Homemaker
                            </option>

                            <option value="unemployed" {{ old('occupation') == 'unemployed' ? 'selected' : '' }}>
                                Unemployed
                            </option>

                            <option value="other" {{ old('occupation') == 'other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>

                        @error('occupation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
