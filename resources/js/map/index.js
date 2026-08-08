function initCinemaMap() {
    const mapElement = document.getElementById('map');
    const dataElement = document.getElementById('cinemas-data');

    // Only run on the Cinema Map page.
    if (!mapElement || !dataElement) return;

    if (!window.google?.maps) {
        console.error('Google Maps API could not be loaded.');
        return;
    }

    let cinemas;

    try {
        cinemas = JSON.parse(dataElement.textContent);
    } catch (error) {
        console.error('Invalid cinema data:', error);
        return;
    }

    const map = new google.maps.Map(mapElement, {
        zoom: 12,
        center: { lat: 35.6762, lng: 139.6503 },
    });

    let activeInfoWindow = null;

    cinemas.forEach((cinema) => {
        const latitude = Number.parseFloat(cinema.latitude);
        const longitude = Number.parseFloat(cinema.longitude);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

        const marker = new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map,
            title: cinema.cinema_name,
        });

        const content = document.createElement('div');
        content.className = 'cinema-map-info';

        const title = document.createElement('h6');
        title.textContent = cinema.cinema_name || 'Cinema';

        const address = document.createElement('p');
        address.textContent = `Address: ${cinema.address || 'N/A'}`;

        const screens = document.createElement('p');
        screens.textContent = `Screens: ${cinema.total_screens ?? 'N/A'}`;

        const phone = document.createElement('p');
        phone.textContent = `Phone: ${cinema.phone || 'N/A'}`;

        content.append(title, address, screens, phone);

        const infoWindow = new google.maps.InfoWindow({ content });

        marker.addListener('click', () => {
            activeInfoWindow?.close();
            infoWindow.open({ map, anchor: marker });
            activeInfoWindow = infoWindow;
        });
    });
}

window.addEventListener('load', initCinemaMap);