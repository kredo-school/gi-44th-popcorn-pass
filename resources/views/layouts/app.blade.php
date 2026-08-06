<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">

    <title>@yield('title', config('app.name', 'Popcorn Pass'))</title>

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@300;700&display=swap"
        rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- CSS and JavaScript --}}
    @vite(['resources/js/app.js'])

    @yield('styles')
    @stack('styles')
</head>

<body id="top">
    <div id="app">
        @include('templates.navbar_main')

        <main>
            <div class="container-fluid px-0">
                <div class="row g-0">
                    <div class="col-12 p-0">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>

</html>