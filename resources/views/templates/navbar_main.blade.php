<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm py-0 sticky-top">
    <div class="container-fluid bg-black">

        <!-- Logo -->
        <a class="navbar-brand m-0 p-0" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="70" height="70">
        </a>

        <!-- home button -->
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- mennu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- center menu -->
            <ul class="navbar-nav mx-auto align-items-lg-center">

                <li class="nav-item">
                    <a href="{{ route('home') }}#Nowplaying" class="nav-link nav-font">
                        Now Playing
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('movie.showtime.display') }}" class="nav-link nav-font">
                        Showtimes
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('home') }}#Comingsoon" class="nav-link nav-font">
                        Coming Soon
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('home') }}#Information" class="nav-link nav-font">
                        Information
                    </a>
                </li>


                <li class="nav-item text-white">
                    <form method="POST" action="{{ route('logout') }}"> @csrf <button type="submit">Logout</button>
                    </form>
                </li>

            </ul>

            <!-- right button -->
            <div class="ms-lg-auto d-flex align-items-center gap-2">

                @auth

                     {{-- Notification button  
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-light position-relative">
                        <i class="bi bi-bell"></i>

                        未読件数がある場合
                        @if (isset($unreadCount) && $unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a> --}}

                    <!-- My Page -->
                    <a href="{{ route('mypage.dashboard') }}" class="btn btn-color mypage-text">
                        <img src="{{ asset('images/mypage.png') }}" width="35" height="35">
                        My Page
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-color mypage-text">
                        <img src="{{ asset('images/mypage.png') }}" width="35" height="35">
                        Log in
                    </a>

                @endauth

            </div>

        </div>

    </div>
</nav>
