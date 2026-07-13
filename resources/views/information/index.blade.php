@extends('layouts.app')
@section('title', 'Information')
@section('content')

<div class="info-detail-page">

    <h2 class="info-title text-center mb-5">Information</h2>

    <div class="container">

        @forelse($information as $info)
            <a href="{{ route('information.detail', $info->id) }}" class="text-decoration-none">
                <div class="info-list-item mb-2">
                    <span class="info-list-badge" style="background-color: {{ $info->category->color }}">
                        {{ $info->category->name }}
                    </span>
                    <span class="info-list-title">{{ $info->title }}</span>
                    <span class="info-list-body">{{ Str::limit($info->content, 60) }}</span>
                    <span class="info-list-arrow">›</span>
                </div>
            </a>
        @empty
            <p class="text-white text-center">No information available.</p>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $information->links() }}
        </div>

    </div>

    {{-- Back Button --}}
    <div class="mt-5">
        <a href="{{ route('home') }}#Information" class="info-back-btn">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </a>
    </div>

</div>

@endsection