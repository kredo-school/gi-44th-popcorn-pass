@extends('layouts.app')

@section('title', '予約方法の選択')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reservation-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/reservation-checkout.css') }}">
@endpush

@section('content')
<div class="reservation-page">

    <div class="container">

        <h1 class="page-title text-center mb-5">予約手続き</h1>

        {{-- Stepper --}}
        <div class="stepper d-flex justify-content-center align-items-center mb-5">
            <div class="step complete text-center">
                <div class="step-circle d-flex align-items-center justify-content-center mx-auto">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-label mt-2">座席選択</div>
            </div>

            <div class="step-line mx-2"></div>

            <div class="step current text-center">
                <div class="step-circle d-flex align-items-center justify-content-center mx-auto">2</div>
                <div class="step-label mt-2">予約方法</div>
            </div>

            <div class="step-line mx-2"></div>

            <div class="step upcoming text-center">
                <div class="step-circle d-flex align-items-center justify-content-center mx-auto">3</div>
                <div class="step-label mt-2">お支払い</div>
            </div>

            <div class="step-line mx-2"></div>

            <div class="step upcoming text-center">
                <div class="step-circle d-flex align-items-center justify-content-center mx-auto">4</div>
                <div class="step-label mt-2">完了</div>
            </div>
        </div>

        <div class="row justify-content-center g-4">

            {{-- Left: Guest / Login Options --}}
            <div class="col-lg-7">

                <h2 class="section-title mb-4">ご予約方法をお選びください</h2>

                {{-- Guest Checkout Card --}}
                <div class="card option-card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user option-icon me-3"></i>
                            <h3 class="option-title mb-0">ゲストとして予約する</h3>
                        </div>
                        <p class="option-desc">会員登録なしで、今すぐ予約手続きに進めます。</p>

                        <form action="{{ route('reservation.guest.store') }}" method="POST" class="mt-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="guest_name" class="form-label">お名前</label>
                                    <input type="text" id="guest_name" name="guest_name"
                                        class="form-control @error('guest_name') is-invalid @enderror"
                                        value="{{ old('guest_name') }}" placeholder="山田 太郎" required>
                                    @error('guest_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="guest_email" class="form-label">メールアドレス</label>
                                    <input type="email" id="guest_email" name="guest_email"
                                        class="form-control @error('guest_email') is-invalid @enderror"
                                        value="{{ old('guest_email') }}" placeholder="example@mail.com" required>
                                    @error('guest_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="guest_phone" class="form-label">電話番号</label>
                                    <input type="tel" id="guest_phone" name="guest_phone"
                                        class="form-control @error('guest_phone') is-invalid @enderror"
                                        value="{{ old('guest_phone') }}" placeholder="090-1234-5678" required>
                                    @error('guest_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="guest_email_confirm" class="form-label">メールアドレス（確認）</label>
                                    <input type="email" id="guest_email_confirm" name="guest_email_confirmation"
                                        class="form-control" placeholder="example@mail.com" required>
                                </div>
                            </div>

                            <button type="submit" class="btn next-btn w-100 mt-4">
                                ゲストとして続ける
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Login Card --}}
                <div class="card option-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-right-to-bracket option-icon me-3"></i>
                            <h3 class="option-title mb-0">ログインして予約する</h3>
                        </div>
                        <p class="option-desc">会員の方はログインすると、ポイント利用や予約履歴の確認ができます。</p>

                        <form action="{{ route('login') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ route('reservation.checkout') }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">メールアドレス</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="example@mail.com" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">パスワード</label>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                                    required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">ログイン状態を保持する</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="forgot-link">パスワードをお忘れですか？</a>
                            </div>

                            <button type="submit" class="btn next-btn w-100">
                                ログインする
                            </button>

                            <p class="text-center mt-3 mb-0">
                                会員登録がまだの方は
                                <a href="{{ route('register') }}" class="register-link">新規会員登録</a>
                            </p>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Right: Order Summary --}}
            <div class="col-lg-4">
                <div class="card summary sticky-top" style="top: 30px;">
                    <div class="card-header">
                        ご予約内容
                    </div>
                    <div class="card-body">
                        <h5 class="mb-1">{{ $movie->title ?? '映画タイトル' }}</h5>
                        <small class="d-block mb-3">{{ $movie->genre ?? 'ジャンル未設定' }}</small>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>劇場</span>
                            <span>{{ $theater->name ?? '〇〇シネマ 新宿' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>上映日時</span>
                            <span>{{ $showtime->date ?? '2026/08/01' }} {{ $showtime->time ?? '19:30' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>スクリーン</span>
                            <span>{{ $screen->name ?? 'スクリーン 3' }}</span>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <span>選択座席</span>
                            <div id="selected-seats" class="mt-2">
                                @forelse($selectedSeats ?? [] as $seat)
                                <span class="seat-tag {{ $seat->is_premium ? 'premium' : 'normal' }}">
                                    {{ $seat->label }}
                                </span>
                                @empty
                                <span class="seat-tag normal">A-12</span>
                                <span class="seat-tag premium">A-13</span>
                                @endforelse
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>チケット料金</span>
                            <span>¥{{ number_format($ticketTotal ?? 3600) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="pay-extra">プレミアム座席追加料金</span>
                            <span class="pay-extra">¥{{ number_format($premiumFee ?? 500) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between total-row">
                            <strong>合計金額</strong>
                            <strong>¥{{ number_format($total ?? 4100) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-5">
            <a href="{{ route('reservation.seats') }}" class="btn back-btn">
                戻る
            </a>
        </div>

    </div>
</div>
@endsection