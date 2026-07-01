@extends('layouts.app')
@section('title', 'Movie Review')
@section('content')


<div class="index">

    <h2 class="text-center text-white fw-bold mb-4">Movie Reviews</h2>

    {{-- Success Message --}}
    @if (sesscion('success'))
        <div class="alert alert-sucess text-center">
            {{ session('success') }}
        </div>        
    @endif

    <div class="row">

        {{-- Left: title & poster --}}
        <div class="col-lg-3 text-center">
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="img-fluid mb-3" style="max-width: 200px">

            @auth
                <a href="{{ route('reviews.create', $movie->id) }}" class="btn review-write-btn w-100">
                    Write a Review
                </a>
            @endauth
        </div>

        {{-- Right: Reviews --}}
        <div class="col-lg-9">
            {{-- Movie title & Rating --}}
            <h3 class="text-white fw-bold mb-2">{{ strtoupper($movie->title) }}</h3>

            <div class="d-flwx align-item-center mb-1">
                @for ($i = 1; $i < 5; $i++)
                    @if ($i <= round($avarageRating))
                        <i class="fa-solid fa-star text-warning"></i>
                    @else
                        <i class="fa-regular fa-star text-warning"></i>
                    @endif
                @endfor
                <span class="test-white fw^bold ms-2">{{ number_foramt($averageRating, 1) }} /5</span>
            </div>

            <p class="text-warning mb-4"></p>

        </div>

    </div>

</div>


    
@endsection