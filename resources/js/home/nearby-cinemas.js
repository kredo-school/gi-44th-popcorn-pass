(function () {
    const STORAGE_KEY = 'locationPermission';

    const overlay = document.getElementById('locationPermissionOverlay');
    const statusEl = document.getElementById('nearbyCinemasStatus');
    const listEl = document.getElementById('nearbyCinemasList');
    const changeBtn = document.getElementById('changeLocationPrefBtn');

    if (!overlay || !statusEl || !listEl || !changeBtn) {
        return;
    }

    /**
     * Show location permission overlay
     */
    function showOverlay() {
        overlay.classList.add('is-visible');
    }

    /**
     * Hide location permission overlay
     */
    function hideOverlay() {
        overlay.classList.remove('is-visible');
    }

    /**
     * Show status message
     */
    function showStatus(message, showSpinner = false) {
        statusEl.classList.remove('is-hidden');
        listEl.classList.add('is-hidden');

        statusEl.innerHTML =
            (showSpinner
                ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>'
                : '') + message;
    }

    /**
     * Escape HTML to prevent unsafe output
     */
    function escapeHtml(value) {
        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;
    }

    /**
     * Render nearby cinema cards
     */
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
                const cinemaName = escapeHtml(cinema.name);
                const cinemaAddress = escapeHtml(cinema.address);

                const source = cinema.source ?? payload?.source ?? 'database';

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

                let destinationUrl = null;
                let buttonText = 'Website Unavailable';
                let externalAttributes = '';

                /**
                 * Database cinema
                 *
                 * Flow:
                 * Cinema card
                 * ↓
                 * /cinemas/{uuid}/visit
                 * ↓
                 * Save selected_cinema_id
                 * ↓
                 * Redirect to official website_url
                 */
                if (source === 'database' && cinema.id) {
                    const cinemaId = encodeURIComponent(cinema.id);

                    destinationUrl = `/cinemas/${cinemaId}/home`;

                    buttonText = 'Visit Official Website';
                }

                /**
                 * Google Places cinema
                 *
                 * Google Places results do not currently have
                 * a Popcorn Pass Cinema UUID.
                 *
                 * Therefore, use Google Maps as fallback.
                 */
                if (source === 'google_places' && cinema.maps_url) {
                    destinationUrl = cinema.maps_url;

                    buttonText = 'Open in Google Maps';

                    externalAttributes =
                        'target="_blank" rel="noopener noreferrer"';
                }

                const safeDestinationUrl = destinationUrl
                    ? escapeHtml(destinationUrl)
                    : null;

                const cinemaContent = `
                    <div class="cinema-card-name">
                        ${cinemaName}
                    </div>

                    <div class="cinema-card-meta">
                        ${cinemaAddress}
                    </div>

                    ${distance}
                `;

                const cinemaMainLink = safeDestinationUrl
                    ? `
                        <a
                            href="${safeDestinationUrl}"
                            class="text-decoration-none d-block"
                            ${externalAttributes}
                        >
                            ${cinemaContent}
                        </a>
                    `
                    : `
                        <div>
                            ${cinemaContent}
                        </div>
                    `;

                const websiteButton = safeDestinationUrl
                    ? `
                        <a
                            href="${safeDestinationUrl}"
                            class="btn btn-sm location-btn location-btn-primary w-100 mt-2"
                            ${externalAttributes}
                        >
                            ${buttonText}
                        </a>
                    `
                    : `
                        <button
                            type="button"
                            class="btn btn-sm location-btn w-100 mt-2"
                            disabled
                        >
                            Website Unavailable
                        </button>
                    `;

                return `
                    <div class="col-md-4 col-lg-3">
                        <div class="cinema-card">

                            ${cinemaMainLink}

                            ${websiteButton}

                        </div>
                    </div>
                `;
            })
            .join('');
    }

    /**
     * Fetch nearby cinemas
     */
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

    /**
     * Use database fallback
     */
    function fetchFallback() {
        fetchCinemas();
    }

    /**
     * Request browser location
     */
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

    /**
     * Handle location permission choice
     */
    function handleChoice(choice) {
        switch (choice) {
            case 'always':
                localStorage.setItem(
                    STORAGE_KEY,
                    'always'
                );

                hideOverlay();

                requestLocationAndFetch();

                break;

            case 'once':
                localStorage.removeItem(
                    STORAGE_KEY
                );

                hideOverlay();

                requestLocationAndFetch();

                break;

            case 'deny':
                localStorage.setItem(
                    STORAGE_KEY,
                    'deny'
                );

                hideOverlay();

                fetchFallback();

                break;

            default:
                break;
        }
    }

    /**
     * Permission buttons
     */
    document
        .querySelectorAll('[data-choice]')
        .forEach((button) => {
            button.addEventListener(
                'click',
                () => {
                    handleChoice(
                        button.dataset.choice
                    );
                }
            );
        });

    /**
     * Change location preference
     */
    changeBtn.addEventListener(
        'click',
        () => {
            showOverlay();
        }
    );

    /**
     * Restore saved preference
     */
    const storedPreference =
        localStorage.getItem(STORAGE_KEY);

    if (storedPreference === 'always') {
        requestLocationAndFetch();

    } else if (storedPreference === 'deny') {
        fetchFallback();

    } else {
        showOverlay();
    }
})();