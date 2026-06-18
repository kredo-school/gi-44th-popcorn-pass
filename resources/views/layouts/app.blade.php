<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-0">
            <div class="container-fluid bg-black">
                <a class="navbar-brand m-0 p-0" href="{{ url('/') }}">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" width="50" height="50">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav flex-row mx-auto align-items-center">
                        <!-- Authentication Links -->
                        <li class="nav-item me-5">
                            <a href="#" class="text-decoration-none text-white nav-font">Now playing</a>
                        </li>
                        <li class="nav-item me-5 ms-5">
                            <a href="#" class="text-decoration-none text-white nav-font">Coming soon</a>
                        </li>
                        <li class="nav-item  ms-5 me-5">
                            <a href="#" class="text-decoration-none text-white nav-font">Food & Drink</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                                <a href="#" class="btn btn-color mypage-text pt-0 pb-0">
                                    <img src="{{ asset('storage/images/mypage.png') }}" alt="mypage"  width="35" height="35"> My Page
                                </a>
                            </li>
                    </ul>
                            

                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>
