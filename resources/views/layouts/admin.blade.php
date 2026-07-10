<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Popcorn Pass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --navy: #1a1f36;
            --navy-light: #232a47;
            --gold: #FFD700;
            --red: #E63946;
        }

        body {
            background-color: var(--navy);
            color: #ffffff;
            font-family: -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .admin-header {
            background-color: var(--navy);
            border-bottom: 2px solid var(--gold);
            padding: 16px 32px;
        }

        .brand-title {
            color: var(--gold);
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 1px;
        }

        .brand-subtitle {
            color: #ffffff;
            font-size: 16px;
            margin-left: 12px;
        }

        .admin-nav {
            margin-left: 32px;
        }

        .admin-nav .nav-link {
            color: #c9ccd6;
            font-size: 15px;
            padding: 6px 14px;
        }

        .admin-nav .nav-link.active {
            color: var(--gold);
            font-weight: 600;
        }

        .admin-nav .nav-link:hover {
            color: var(--gold);
        }

        .admin-date {
            color: #c9ccd6;
            font-size: 14px;
        }

        .admin-content {
            padding: 24px 32px;
        }

        .card-dark {
            background-color: var(--navy-light);
            border: none;
            border-radius: 8px;
            color: #ffffff;
        }
    </style>

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