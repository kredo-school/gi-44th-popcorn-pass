{{-- resources/views/layouts/mypage.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'My Page') - Popcorn Pass</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
    @stack('styles')
</head>
<body class="mypage-body">

    <nav class="mypage-site-header navbar navbar-expand-md py-2 px-4 d-flex justify-content-between">
        <a href="{{ url('/') }}" class="navbar-brand m-0 p-0">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="50" height="50">
        </a>

        <ul class="navbar-nav flex-row gap-4 d-none d-md-flex">
            <li class="nav-item"><a href="#Nowplaying" class="mypage-nav-link">Now Playing</a></li>
            <li class="nav-item"><a href="{{ route('movie.showtime.display') }}" class="mypage-nav-link">Showtimes</a></li>
            <li class="nav-item"><a href="#Comingsoon" class="mypage-nav-link">Coming Soon</a></li>
            <li class="nav-item"><a href="#FoodDrink" class="mypage-nav-link">Food &amp; Drink</a></li>
        </ul>

        <span class="mypage-nav-badge">My Page</span>
    </nav>

    <div class="mypage-shell d-flex">
        <aside class="mypage-sidebar p-4">
            @include('mypage.components.sidebar')
        </aside>

        <main class="mypage-main flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    @stack('scripts')
</body>
</html>