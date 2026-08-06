<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'My Page') - Popcorn Pass</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@300;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">

    @vite(['resources/js/app.js'])

    @stack('styles')
</head>

<body class="mypage-body">

    @include('templates.navbar_main')

    <div class="mypage-shell d-flex">
        <aside class="mypage-sidebar p-4">
            @include('mypage.components.sidebar')
        </aside>

        <main class="mypage-main flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>