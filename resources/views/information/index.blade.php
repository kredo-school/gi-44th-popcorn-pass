@extends('layouts.app')
@section('title', 'Information')
@section('content')

<div class="info-detail-page">

    <h2 class="info-title text-center mb-5">Information</h2>

    <div class="container">
    
        @forelse($information as $info)
    
        <a href="{{ route('information.detail', $info->id) }}" class="text-decoration-none">
    
            <div class="info-list-item mb-2">
    
                {{-- Category --}}
                <span class="info-list-badge" style="
                            background-color: {{ $info->category->color }};
                            color: {{ $info->category->text_color }};
                        ">
                    {{ $info->category->name }}
                </span>
    
                {{-- Title --}}
                <span class="info-list-title">
                    {{ $info->title }}
                </span>
    
                {{-- Published Date --}}
                <span class="info-list-date">
                    {{ $info->published_at->format('Y.m.d') }}
                </span>
    
                {{-- Arrow --}}
                <span class="info-list-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
    
            </div>
    
        </a>
    
        @empty
    
        <p class="text-white text-center">
            No information available.
        </p>
    
        @endforelse
    
    
        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $information->links() }}
        </div>
    
    </div>

    {{-- Back Button --}}
    <div class="mt-5">
        <button type="button" class="info-back-btn" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </button>
    </div>

</div>

@endsection