<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-0 sticky-top">
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
                        <a href="{{ route('home') }}#Nowplaying" class="text-decoration-none nav-font">
                            Now Playing
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('movie.showtime.display') }}" class="text-decoration-none nav-font">
                            Showtimes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('home') }}#Comingsoon" class="text-decoration-none nav-font">
                            Coming Soon
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('home') }}#FoodDrink" class="text-decoration-none nav-font">
                            Food & Drink
                        </a>

                    </li>
                    <li class="nav-item">
                        <a href="{{ route('home') }}#Information" class="text-decoration-none nav-font">
                            Information
                        </a>

                    </li>
                    <li class="nav-item text-white">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </li>


                </ul>

                <!-- My Page -->
                <div class="ms-auto me-3">
                    @auth
                        <a href="{{ route('mypage.dashboard') }}" class="btn btn-color mypage-text pt-0 pb-0">
                            <img src="{{ asset('images/mypage.png') }}" alt="mypage" width="35" height="35">
                            My Page
                        </a>
                    @else
                        <a href="/login" class="btn btn-color mypage-text pt-0 pb-0">
                            <img src="{{ asset('images/mypage.png') }}" alt="mypage" width="35" height="35">
                            Log in
                        </a>
                    @endauth
                </div>


            </div>
        </div>
</nav>
