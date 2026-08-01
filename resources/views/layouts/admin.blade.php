<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - Popcorn Pass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite([
    'resources/sass/admin.scss',
    'resources/js/admin.js'
    ])

    @yield('styles')
</head>
<body>

    <header class="admin-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <span class="brand-title">POPCORN PASS</span>
            <span class="brand-subtitle">@yield('page-title', 'Admin Dashboard')</span>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-link d-inline {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.movies') }}" class="nav-link d-inline {{ request()->routeIs('admin.movies*') ? 'active' : '' }}">Movies</a>
                <a href="{{ route('admin.information') }}"
                    class="nav-link d-inline {{ request()->routeIs('admin.information*') ? 'active' : '' }}">Information</a>
                <a href="{{ route('admin.reservations') }}" class="nav-link d-inline {{ request()->routeIs('admin.reservations*') ? 'active' : '' }}">Reservations</a>
                <a href="{{ route('admin.users') }}" class="nav-link d-inline {{ request()->routeIs('admin.users*') ? 'active' : '' }}">Users</a>
                <a href="{{ route('admin.coupons-promotions') }}" class="nav-link d-inline {{ request()->routeIs('admin.coupons-promotions*') ? 'active' : '' }}">Coupons</a>
                <a href="{{ route('admin.analytics') }}" class="nav-link d-inline {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">Analytics</a>
                <a href="{{ route('admin.reviews') }}"
                    class="nav-link d-inline {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">Reviews</a>
                <a href="{{ route('admin.settings') }}" class="nav-link d-inline {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">Settings</a>
                <a href="{{ route('admin.chat.index') }}" class="nav-link d-inline {{ request()->routeIs('admin.chat*') ? 'active' : '' }}"> Chat</a>
            </nav>
        </div>

        <span class="admin-date">{{ now()->format('Y-m-d') }}</span>
    </header>

    <main class="admin-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>