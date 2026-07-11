@extends('layouts.app')
@section('title', $information->title)
@section('content')

<div class="info-detail-page">

    <h2 class="info-detail-title text-center mb-5" style="color: {{ $information->category->color }}">
        {{ $information->category->name }}
    </h2>
    <div class="container">
        <div class="row align-items-start">

            {{-- Left: Image --}}
            <div class="col-lg-4 text-center">
                @if($information->image_url)
                    <img src="{{ $information->image_url }}" alt="{{ $information->title }}"
                        class="img-fluid rounded info-detail-img">
                @endif
            </div>

            {{-- Right: Content --}}
            <div class="col-lg-8">
                <h3 class="info-detail-heading mb-4">{{ strtoupper($information->title) }}</h3>
                <p class="info-detail-body">{!! nl2br(e($information->content)) !!}</p>
            </div>

        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-5">
        <a href="{{ route('information.index') }}" class="info-back-btn">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </a>
    </div>

</div>

@endsection