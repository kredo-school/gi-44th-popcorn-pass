<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Popcorn Pass') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body id="top">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-0">
            <div class="container-fluid bg-black">

                <!-- Logo -->
                <a class="navbar-brand m-0 p-0" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="70" height="70">
                </a>

                <div class="container-fluid px-0">
                    <div class="collapse navbar-collapse d-flex" id="navbarSupportedContent">

                        <!-- Center Links -->
                        <ul class="navbar-nav flex-row position-absolute start-50 translate-middle-x">

                            <li class="nav-item">
                                <a href="#Nowplaying" class="text-decoration-none nav-font">
                                    Now Playing
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('movie.showtime.display')}}" class="text-decoration-none nav-font">
                                    Showtimes
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#Comingsoon" class="text-decoration-none nav-font">
                                    Coming Soon
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#FoodDrink" class="text-decoration-none nav-font">
                                    Food & Drink
                                </a>
                                
                            </li>

                        </ul>

                        <!-- My Page -->
                        <div class="ms-auto me-3">
                            @auth
                                <a href="#" class="btn btn-color mypage-text pt-0 pb-0">
                                    <img src="{{ asset('images/mypage.png') }}" alt="mypage" width="35"
                                        height="35">
                                    My Page
                                </a>
                            @else
                                <a href="/login" class="btn btn-color mypage-text pt-0 pb-0">
                                    <img src="{{ asset('images/mypage.png') }}" alt="mypage" width="35"
                                        height="35">
                                    Log in
                                </a>
                            @endauth
                        </div>
                        

                    </div>
                </div>
        </nav>

        <main class="">
            <div class="container-fluid px-0">
                <div class="row g-0 ">
                    <div class="col-12 p-0">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
