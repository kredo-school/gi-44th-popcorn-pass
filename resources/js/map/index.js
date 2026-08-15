function initCinemaMap() {
    const mapElement = document.getElementById('map');
    const dataElement = document.getElementById('cinemas-data');
    const selectedCinemaDataElement = document.getElementById('selected-cinema-data');
    const cinemaHomeUrlTemplate = mapElement?.dataset.cinemaHomeUrl;

    // Only run on the Cinema Map page.
    if (!mapElement || !dataElement) return;

    if (!window.google?.maps) {
        console.error('Google Maps API could not be loaded.');
        return;
    }

    let cinemas;
    let selectedCinemaId = null;

    if (selectedCinemaDataElement) {
        try {
            selectedCinemaId = JSON.parse(selectedCinemaDataElement.textContent);
        } catch (error) {
            console.error('Invalid selected cinema data:', error);
        }
    }

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
        const isSelected = String(cinema.id) === String(selectedCinemaId);
        const latitude = Number.parseFloat(cinema.latitude);
        const longitude = Number.parseFloat(cinema.longitude);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

        const marker = new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map,
            title: cinema.cinema_name,
            icon: isSelected ? {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: '#FFC107',
                fillOpacity: 1,
                strokeColor: '#111827',
                strokeWeight: 3,
            } : undefined,
        });

        const content = document.createElement('div');
        content.className = 'cinema-map-info';

        const title = document.createElement('h6');
        title.textContent = cinema.cinema_name || 'Cinema';

        if (isSelected) {
            const selectedBadge = document.createElement('span');
            selectedBadge.className = 'badge bg-warning text-dark mb-2';
            selectedBadge.textContent = 'CURRENT CINEMA';
            content.append(title, selectedBadge);
        } else {
            content.append(title);
        }

        const address = document.createElement('p');
        address.textContent = `Address: ${cinema.address || 'N/A'}`;

        const screens = document.createElement('p');
        screens.textContent = `Screens: ${cinema.total_screens ?? 'N/A'}`;

        const phone = document.createElement('p');
        phone.textContent = `Phone: ${cinema.phone || 'N/A'}`;

        content.append(address, screens, phone);

        if (cinemaHomeUrlTemplate && cinema.id) {
            const selectButton = document.createElement('a');
            selectButton.className = 'btn btn-warning btn-sm mt-2';
            selectButton.textContent = 'Select This Cinema';
            selectButton.href = cinemaHomeUrlTemplate.replace('__CINEMA_ID__', cinema.id);
            content.append(selectButton);
        }

        const infoWindow = new google.maps.InfoWindow({ content });

        marker.addListener('click', () => {
            activeInfoWindow?.close();
            infoWindow.open({ map, anchor: marker });
            activeInfoWindow = infoWindow;
        });
    });
}

window.addEventListener('load', initCinemaMap);