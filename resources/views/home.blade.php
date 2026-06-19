@extends('layouts.app')

@section('content')
    <div>
        <img src="{{ asset('storage/images/welcome.png') }}" alt="welcome.image" style="width: 100%; display: block;">
    </div>
    <div class="mt-0" style="
    background-image: url('{{ asset('storage/images/home_back.png') }}');
    background-size: cover;
    background-position: center top;
    background-repeat: no-repeat;
    width: 100%;
">
        <div>
            <div class="container text-center ">
                {{-- [Soon] search label --}}
            </div>
            {{-- Top Ranking --}}
            <div class="container-fuild">
                <p class="display-3 text-white base ms-5 ">
                    👑 Top Ranking
                </p>

                {{-- Top 3 --}}
                <div class="row justify-content-center align-items-end g-5 mt-5">

    {{-- 1 --}}
    <div class="col-10 col-md-3 text-center" style="position: relative;">
        <div class="ranking-title" style="
            position: absolute;
            top: -6vw;
            left: -1vw;
            font-size: clamp(8rem, 20vw, 20rem);
            line-height: 1;
            z-index: 1;">1
        </div>
        <div class="card">
            <div class="card-header p-0 border-0" style="height: 35vw; max-height: 600px; overflow: hidden;">
                <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                    class="w-100 h-100" style="object-fit: cover;">
            </div>
            <div class="card-body back-gray">
                <p class="text-dark">Pirates of caribbean</p>
            </div>
        </div>
    </div>

    {{-- 2 --}}
    <div class="col-10 col-md-3 text-center" style="position: relative;">
        <div class="ranking-title" style="
            position: absolute;
            top: -5vw;
            left: 0vw;
            font-size: clamp(3rem, 15vw, 15rem);
            line-height: 1;
            z-index: 1;">2
        </div>
        <div class="card">
            <div class="card-header p-0 border-0" style="height: 32vw; max-height: 560px; overflow: hidden;">
                <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                    class="w-100 h-100" style="object-fit: cover;">
            </div>
            <div class="card-body back-gray">
                <p class="text-dark">Pirates of caribbean</p>
            </div>
        </div>
    </div>

    {{-- 3 --}}
    <div class="col-10 col-md-3 text-center" style="position: relative;">
        <div class="ranking-title" style="
            position: absolute;
            top: -3vw;
            left: 0vw;
            font-size: clamp(2rem, 10vw, 10rem);
            line-height: 1;
            z-index: 1;">3
        </div>
        <div class="card">
            <div class="card-header p-0 border-0" style="height: 29vw; max-height: 540px; overflow: hidden;">
                <img src="{{ asset('storage/images/movie.png') }}" alt="Pirates of caribbean"
                    class="w-100 h-100" style="object-fit: cover;">
            </div>
            <div class="card-body back-gray">
                <p class="text-dark">Pirates of caribbean</p>
            </div>
        </div>
    </div>

</div>

            </div>
        </div>
    </div>
@endsection
