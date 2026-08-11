@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div id="map" class="cinema-map"></div>
    </div>

    <script id="cinemas-data" type="application/json">
        @json($cinemas)
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}" defer></script>

    
@endsection