@extends('layouts.mypage')

@section('title', 'Edit Profile')

@section('content')
    <div class="mb-4">
        <h2><i class="fa-solid fa-pen me-2"></i>Edit Profile</h2>
    </div>

    <div class="mypage-card p-4">
        <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="text-center mb-4 pb-4" style="border-bottom: 1px solid #2a2f4a;">
                <div class="mypage-profile-avatar-wrapper mx-auto mb-3">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}"
                             alt="{{ $user->full_name }}"
                             class="mypage-profile-avatar rounded-circle"
                             id="avatarPreview">
                    @else
                        <div class="mypage-profile-avatar mypage-profile-avatar-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center"
                             id="avatarPreview">
                            <i class="fa-solid fa-user fa-3x"></i>
                        </div>
                    @endif
                </div>
                <label for="avatar" class="btn text-warning border-warning btn-sm">
                    <i class="fa-solid fa-camera me-1"></i>Change Photo
                </label>
                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                @error('avatar')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Fields --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control mypage-form-input @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control mypage-form-input @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Email</label>
                <input type="email" name="email"
                       class="form-control mypage-form-input @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Phone</label>
                <input type="text" name="phone"
                       class="form-control mypage-form-input @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Gender</label>
                <select name="gender" class="form-select mypage-form-input">
                    <option value="" {{ !$user->gender ? 'selected' : '' }}>— Select —</option>
                    <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ $user->gender === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label small text-muted">Occupation</label>
                <input type="text" name="occupation"
                       class="form-control mypage-form-input"
                       value="{{ old('occupation', $user->occupation) }}">
            </div>

            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('mypage.profile') }}" class="btn text-danger border-danger px-4">
                    Cancel
                </a>
                <button type="submit" class="btn text-warning border-warning px-4">
                    <i class="fa-solid fa-check me-1"></i>Update
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('mypage.profile') }}" class="btn text-white border-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
@endsection
