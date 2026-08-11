(function () {
    const STORAGE_KEY = 'locationPermission';

    const overlay = document.getElementById('locationPermissionOverlay');
    const statusEl = document.getElementById('nearbyCinemasStatus');
    const listEl = document.getElementById('nearbyCinemasList');
    const changeBtn = document.getElementById('changeLocationPrefBtn');


    if (!overlay || !statusEl || !listEl || !changeBtn) {
        return;
    }

    function showOverlay() {
        overlay.classList.add('is-visible');
    }

    function hideOverlay() {
        overlay.classList.remove('is-visible');
    }

    function showStatus(message, showSpinner = false) {
        statusEl.classList.remove('is-hidden');
        listEl.classList.add('is-hidden');

        statusEl.innerHTML =
            (showSpinner
                ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>'
                : '') + message;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;
    }

    function renderCinemas(payload) {
        const cinemas = payload?.cinemas ?? [];

        if (cinemas.length === 0) {
            showStatus('No cinemas found nearby.');
            return;
        }

        statusEl.classList.add('is-hidden');
        listEl.classList.remove('is-hidden');

        listEl.innerHTML = cinemas
            .map((cinema) => {
                const cinemaId = encodeURIComponent(cinema.id);
                const cinemaName = escapeHtml(cinema.name);
                const cinemaAddress = escapeHtml(cinema.address);

                const hasDistance =
                    cinema.distance_km !== null &&
                    cinema.distance_km !== undefined;

                const distance = hasDistance
                    ? `
                        <div class="cinema-card-distance mb-2">
                            ${escapeHtml(cinema.distance_km)} km away
                        </div>
                    `
                    : '';

                const websiteButton = cinema.maps_url
                    ? `
                        <a
                            href="${escapeHtml(cinema.maps_url)}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-sm location-btn location-btn-primary w-100 mt-2"
                        >
                            Visit Website
                        </a>
                    `
                    : '';

                return `
                    <div class="col-md-4 col-lg-3">
                        <div class="cinema-card">
                            <a
                                href="/cinemas/${cinemaId}"
                                class="text-decoration-none d-block"
                            >
                                <div class="cinema-card-name">
                                    ${cinemaName}
                                </div>

                                <div class="cinema-card-meta">
                                    ${cinemaAddress}
                                </div>

                                ${distance}
                            </a>

                            ${websiteButton}
                        </div>
                    </div>
                `;
            })
            .join('');
    }

    function fetchCinemas(lat = null, lng = null) {
        showStatus('Loading nearby theaters...', true);

        const params = new URLSearchParams();

        if (lat !== null && lng !== null) {
            params.set('lat', lat);
            params.set('lng', lng);
        }

        const queryString = params.toString();

        const url = queryString
            ? `/api/nearby-cinemas?${queryString}`
            : '/api/nearby-cinemas';

        fetch(url, {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `Nearby cinemas request failed: ${response.status}`
                    );
                }

                return response.json();
            })
            .then(renderCinemas)
            .catch((error) => {
                console.error(error);

                showStatus(
                    'Could not load theaters right now.'
                );
            });
    }

    function fetchFallback() {
        fetchCinemas();
    }

    function requestLocationAndFetch() {
        if (!navigator.geolocation) {
            fetchFallback();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                fetchCinemas(
                    position.coords.latitude,
                    position.coords.longitude
                );
            },
            () => {

                fetchFallback();
            },
            {
                timeout: 8000,
            }
        );
    }

    function handleChoice(choice) {
        switch (choice) {
            case 'always':
                localStorage.setItem(STORAGE_KEY, 'always');
                hideOverlay();
                requestLocationAndFetch();
                break;

            case 'once':
                localStorage.removeItem(STORAGE_KEY);
                hideOverlay();
                requestLocationAndFetch();
                break;

            case 'deny':
                localStorage.setItem(STORAGE_KEY, 'deny');
                hideOverlay();
                fetchFallback();
                break;

            default:
                break;
        }
    }

    document.querySelectorAll('[data-choice]').forEach((button) => {
        button.addEventListener('click', () => {
            handleChoice(button.dataset.choice);
        });
    });

    changeBtn.addEventListener('click', () => {
        showOverlay();
    });

    const storedPreference = localStorage.getItem(STORAGE_KEY);

    if (storedPreference === 'always') {
        requestLocationAndFetch();
    } else if (storedPreference === 'deny') {
        fetchFallback();
    } else {
        showOverlay();
    }
})();