<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm py-0 sticky-top">
    <div class="container-fluid bg-black">



        <!-- mennu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- menu bar-->
            <button id="menuBtn" class="menu-btn me-3">
                <i class="fa-solid fa-bars"></i>
                <span class="menu-text">MENU</span>
            </button>

<!-- Logo + Selected Cinema -->
@php
    $selectedCinema = null;

    $selectedCinemaId = session('selected_cinema_id');

    if ($selectedCinemaId) {
        $selectedCinema = \App\Models\Cinema::where('is_active', true)
            ->find($selectedCinemaId);
    }
@endphp

<a
    class="navbar-brand m-0 p-0 d-flex align-items-center text-decoration-none"
    href="{{ $selectedCinema
        ? route('cinemas.home', $selectedCinema)
        : url('/') }}"
>
    <img
        src="{{ asset('images/layouts/logo.png') }}"
        alt="Popcorn Pass"
        width="70"
        height="70"
    >

    @if($selectedCinema)
        <span class="ms-2 text-white fw-bold">
            - {{ $selectedCinema->cinema_name }}
        </span>
    @endif
</a>
            <!-- right button -->
            <div class="ms-lg-auto d-flex align-items-center gap-2">

                @auth

                    <!-- My Page -->
                    <a href="{{ route('mypage.dashboard') }}" class="btn btn-color mypage-text">
                        <img src="{{ asset('images/layouts/mypage.png') }}" width="35" height="35">
                        My Page
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-color mypage-text">
                        <img src="{{ asset('images/layouts/mypage.png') }}" width="35" height="35">
                        Log in
                    </a>

                @endauth

            </div>

        </div>

    </div>
</nav>


<!-- Sidebar -->
<div id="sidebar" class="sidebar">

    <button id="closeBtn" class="close-btn">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <a href="{{ route('home') }}" class="bar-item">
        <i class="fa-solid fa-house"></i>
        Top Page
    </a>

   
    <div class="row">
        <div class="col-lg-6 text-center">
            <a href="#" class="sidebar-search-item">
                <i class="fa-solid fa-map-location"></i><br>
                Location
            </a>
        </div>

        <div class="col-lg-6 text-center">
            <a href="{{ route('home') }}#Nowshowing" class="sidebar-search-item">
                <i class="fa-solid fa-film"></i><br>
                Movie
            </a>
        </div>
    </div>


    <a href="{{ route('movie.showtime.display') }}" class="bar-item">
        <i class="fa-solid fa-calendar-check"></i>
        Showtimes
    </a>


    <a href="{{ route('information.index')}}" class="bar-item">
        <i class="fa-solid fa-circle-exclamation"></i>
        Information
    </a>
    <a href="{{ route('customer.chat.index')}}" class="bar-item">
        <i class="fa-solid fa-circle-question"></i>
        Contact
    </a>

    @auth
        <a href="{{ route('mypage.dashboard') }}" class="bar-item">
            <i class="fa-solid fa-user"></i>
            My Page
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sidebar-btn text-dark">
                Logout
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="sidebar-btn text-dark">
            Log in
        </a>

    @endauth



</div>

<div id="overlay" class="overlay"></div>
