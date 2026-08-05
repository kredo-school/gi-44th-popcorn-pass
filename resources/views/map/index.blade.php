@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div id="map" style="width: 100%; height: 100vh;"></div>
</div>

<script>
    // Cinema data from Laravel
    const cinemasData = @json($cinemas);
    
    let map;
    
    function initMap() {
        // Center on Tokyo
        const centerTokyo = { lat: 35.6762, lng: 139.6503 };
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: centerTokyo,
        });
        
        // Add markers for each cinema
        cinemasData.forEach(cinema => {
            const markerPosition = {
                lat: parseFloat(cinema.latitude),
                lng: parseFloat(cinema.longitude)
            };
            
            const marker = new google.maps.Marker({
                position: markerPosition,
                map: map,
                title: cinema.cinema_name,
            });
            
            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h6 style="margin: 0 0 8px 0;">${cinema.cinema_name}</h6>
                        <p style="margin: 0 0 8px 0; font-size: 0.9em;">
                            <strong>Address:</strong> ${cinema.address}
                        </p>
                        <p style="margin: 0 0 8px 0; font-size: 0.9em;">
                            <strong>Screens:</strong> ${cinema.total_screens}
                        </p>
                        <p style="margin: 0; font-size: 0.9em;">
                            <strong>Phone:</strong> ${cinema.phone || 'N/A'}
                        </p>
                    </div>
                `
            });
            
            marker.addListener('click', () => {
                // Close all other info windows
                document.querySelectorAll('.gm-ui-hover-effect').forEach(el => {
                    el.style.display = 'none';
                });
                infoWindow.open(map, marker);
            });
        });
    }
    
    // Load the map when the page loads
    window.addEventListener('load', initMap);
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}"></script>
@endsection