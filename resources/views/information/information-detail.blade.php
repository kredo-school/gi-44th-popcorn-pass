@extends('layouts.app')
@section('title', $information->title)

@section('content')

<div class="info-detail-page">

    {{-- Category --}}
    <div class="text-center mb-4">
        <span class="info-detail-category" style="
                background-color: {{ $information->category->color }};
                color: {{ $information->category->text_color }};
            ">
            {{ $information->category->name }}
        </span>
    </div>

    {{-- Detail Card --}}
    <div class="info-detail-card">

        {{-- Image --}}
        @if($information->image)
            <div class="info-detail-image-wrap">
                <img src="{{ asset($information->image) }}" alt="{{ $information->title }}"
                    class="rounded info-detail-img">
            </div>
        @endif


        {{-- Content --}}
        <div class="info-detail-content">

            <h3 class="info-detail-heading mb-3">
                {{ strtoupper($information->title) }}
            </h3>

            <div class="info-detail-date mb-4">
                {{ $information->published_at->format('Y.m.d') }}
            </div>

            <p class="info-detail-body">
                {!! nl2br(e($information->content)) !!}
            </p>

        </div>

    </div>


    {{-- Back Button --}}
    <a href="{{ route('information.index') }}" class="info-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
        BACK
    </a>

</div>

@endsection