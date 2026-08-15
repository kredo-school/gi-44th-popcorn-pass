@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div id="map" class="cinema-map" data-cinema-home-url="{{ route('cinemas.home', ['cinema' => '__CINEMA_ID__']) }}"></div>
    </div>

    <script id="selected-cinema-data" type="application/json">
        @json($selectedCinemaId)
    </script>

    <script id="cinemas-data" type="application/json">
        @json($cinemas)
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}" defer></script>

    
@endsection