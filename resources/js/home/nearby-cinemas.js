(function () {
    const STORAGE_KEY = 'locationPermission';

    const overlay = document.getElementById('locationPermissionOverlay');
    const statusEl = document.getElementById('nearbyCinemasStatus');
    const listEl = document.getElementById('nearbyCinemasList');
    const changeBtn = document.getElementById('changeLocationPrefBtn');
    const mapEl = document.getElementById('nearbyCinemaMap');

    let miniMap = null;
    let activeInfoWindow = null;
    let userLocationMarker = null;

    /**
     * Get currently selected cinema ID from URL.
     *
     * Example:
     * /cinemas/{uuid}/home
     */
    function getSelectedCinemaId() {
        const match = window.location.pathname.match(
            /^\/cinemas\/([^/]+)\/home\/?$/
        );

        return match ? decodeURIComponent(match[1]) : null;
    }

    const selectedCinemaId = getSelectedCinemaId();

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
     * Read latitude from cinema payload.
     *
     * Supports both:
     * latitude / longitude
     * lat / lng
     */
    function getCinemaLatitude(cinema) {
        const value =
            cinema.latitude ??
            cinema.lat ??
            null;

        const latitude = Number.parseFloat(value);

        return Number.isFinite(latitude)
            ? latitude
            : null;
    }

    function getCinemaLongitude(cinema) {
        const value =
            cinema.longitude ??
            cinema.lng ??
            null;

        const longitude = Number.parseFloat(value);

        return Number.isFinite(longitude)
            ? longitude
            : null;
    }

    /**
     * Wait until Google Maps API has loaded.
     */
    function waitForGoogleMaps(callback, attempt = 0) {
        if (window.google?.maps) {
            callback();
            return;
        }

        if (attempt >= 50) {
            console.error(
                'Google Maps API could not be loaded for the Home mini map.'
            );
            return;
        }

        window.setTimeout(() => {
            waitForGoogleMaps(
                callback,
                attempt + 1
            );
        }, 100);
    }

    /**
     * Create selected cinema marker icon.
     */
    function selectedCinemaIcon() {
        return {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: '#FFC107',
            fillOpacity: 1,
            strokeColor: '#111827',
            strokeWeight: 3,
        };
    }

    /**
     * Create user location marker icon.
     */
    function userLocationIcon() {
        return {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 9,
            fillColor: '#2563EB',
            fillOpacity: 1,
            strokeColor: '#FFFFFF',
            strokeWeight: 3,
        };
    }

    /**
     * Create Mini Map.
     */
    function renderMiniMap(
        cinemas,
        userLatitude = null,
        userLongitude = null
    ) {
        if (!mapEl) {
            return;
        }

        waitForGoogleMaps(() => {
            const validCinemas = cinemas.filter((cinema) => {
                return (
                    getCinemaLatitude(cinema) !== null &&
                    getCinemaLongitude(cinema) !== null
                );
            });

            const selectedCinema = validCinemas.find((cinema) => {
                return (
                    cinema.id &&
                    selectedCinemaId &&
                    String(cinema.id) === String(selectedCinemaId)
                );
            });

            let center = {
                lat: 35.6762,
                lng: 139.6503,
            };

            if (selectedCinema) {
                center = {
                    lat: getCinemaLatitude(selectedCinema),
                    lng: getCinemaLongitude(selectedCinema),
                };
            } else if (
                userLatitude !== null &&
                userLongitude !== null
            ) {
                center = {
                    lat: Number(userLatitude),
                    lng: Number(userLongitude),
                };
            } else if (validCinemas.length > 0) {
                center = {
                    lat: getCinemaLatitude(validCinemas[0]),
                    lng: getCinemaLongitude(validCinemas[0]),
                };
            }

            miniMap = new google.maps.Map(mapEl, {
                center,
                zoom: 12,

                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,

                zoomControl: true,

                gestureHandling: 'cooperative',
            });

            activeInfoWindow = null;

            const bounds = new google.maps.LatLngBounds();

            /**
             * Cinema markers
             */
            validCinemas.forEach((cinema) => {
                const latitude =
                    getCinemaLatitude(cinema);

                const longitude =
                    getCinemaLongitude(cinema);

                const isSelected =
                    cinema.id &&
                    selectedCinemaId &&
                    String(cinema.id) === String(selectedCinemaId);

                const marker = new google.maps.Marker({
                    position: {
                        lat: latitude,
                        lng: longitude,
                    },

                    map: miniMap,

                    title:
                        cinema.name ??
                        cinema.cinema_name ??
                        'Cinema',

                    icon: isSelected
                        ? selectedCinemaIcon()
                        : undefined,

                    zIndex: isSelected
                        ? 100
                        : 10,
                });

                bounds.extend(marker.getPosition());

                const content =
                    document.createElement('div');

                content.className =
                    'cinema-map-info';

                const title =
                    document.createElement('h6');

                title.textContent =
                    cinema.name ??
                    cinema.cinema_name ??
                    'Cinema';

                content.append(title);

                if (isSelected) {
                    const badge =
                        document.createElement('span');

                    badge.className =
                        'badge bg-warning text-dark mb-2';

                    badge.textContent =
                        'CURRENT CINEMA';

                    content.append(badge);
                }

                const address =
                    document.createElement('p');

                address.textContent =
                    cinema.address ??
                    'Address unavailable';

                content.append(address);

                if (
                    cinema.source !== 'google_places' &&
                    cinema.id
                ) {
                    const button =
                        document.createElement('a');

                    button.href =
                        `/cinemas/${encodeURIComponent(cinema.id)}/home`;

                    button.className =
                        'btn btn-warning btn-sm mt-2';

                    button.textContent =
                        isSelected
                            ? 'Current Cinema'
                            : 'Select This Cinema';

                    content.append(button);
                }

                if (
                    cinema.source === 'google_places' &&
                    cinema.maps_url
                ) {
                    const button =
                        document.createElement('a');

                    button.href =
                        cinema.maps_url;

                    button.target =
                        '_blank';

                    button.rel =
                        'noopener noreferrer';

                    button.className =
                        'btn btn-warning btn-sm mt-2';

                    button.textContent =
                        'Open in Google Maps';

                    content.append(button);
                }

                const infoWindow =
                    new google.maps.InfoWindow({
                        content,
                    });

                marker.addListener(
                    'click',
                    () => {
                        activeInfoWindow?.close();

                        infoWindow.open({
                            map: miniMap,
                            anchor: marker,
                        });

                        activeInfoWindow =
                            infoWindow;
                    }
                );
            });

            /**
             * User location marker
             */
            if (
                userLatitude !== null &&
                userLongitude !== null
            ) {
                const latitude =
                    Number.parseFloat(userLatitude);

                const longitude =
                    Number.parseFloat(userLongitude);

                if (
                    Number.isFinite(latitude) &&
                    Number.isFinite(longitude)
                ) {
                    userLocationMarker =
                        new google.maps.Marker({
                            position: {
                                lat: latitude,
                                lng: longitude,
                            },

                            map: miniMap,

                            title: 'Your Location',

                            icon: userLocationIcon(),

                            zIndex: 200,
                        });

                    bounds.extend(
                        userLocationMarker.getPosition()
                    );
                }
            }

            /**
             * Fit all markers inside the mini map.
             */
            if (!bounds.isEmpty()) {
                miniMap.fitBounds(bounds);

                google.maps.event.addListenerOnce(
                    miniMap,
                    'idle',
                    () => {
                        if (miniMap.getZoom() > 14) {
                            miniMap.setZoom(14);
                        }
                    }
                );
            }
        });
    }

    /**
     * Render nearby cinema cards.
     */
    function renderCinemas(
        payload,
        userLatitude = null,
        userLongitude = null
    ) {
        const cinemas =
            payload?.cinemas ?? [];

        if (cinemas.length === 0) {
            showStatus(
                'No cinemas found nearby.'
            );

            renderMiniMap(
                [],
                userLatitude,
                userLongitude
            );

            return;
        }

        statusEl.classList.add(
            'is-hidden'
        );

        listEl.classList.remove(
            'is-hidden'
        );

        listEl.innerHTML = cinemas
            .map((cinema) => {
                const cinemaName =
                    escapeHtml(
                        cinema.name ??
                        cinema.cinema_name
                    );

                const cinemaAddress =
                    escapeHtml(
                        cinema.address
                    );

                const source =
                    cinema.source ??
                    payload?.source ??
                    'database';

                const isSelected =
                    source === 'database' &&
                    cinema.id &&
                    selectedCinemaId &&
                    String(cinema.id) ===
                        String(selectedCinemaId);

                const hasDistance =
                    cinema.distance_km !== null &&
                    cinema.distance_km !== undefined;

                const distance =
                    hasDistance
                        ? `
                            <div class="cinema-card-distance mb-2">
                                ${escapeHtml(cinema.distance_km)} km away
                            </div>
                        `
                        : '';

                let destinationUrl = null;

                let buttonText =
                    'Website Unavailable';

                let externalAttributes =
                    '';

                /**
                 * Database Cinema
                 */
                if (
                    source === 'database' &&
                    cinema.id
                ) {
                    const cinemaId =
                        encodeURIComponent(
                            cinema.id
                        );

                    destinationUrl =
                        `/cinemas/${cinemaId}/home`;

                    buttonText =
                        isSelected
                            ? 'Current Cinema'
                            : 'Select This Cinema';
                }

                /**
                 * Google Places Cinema
                 */
                if (
                    source === 'google_places' &&
                    cinema.maps_url
                ) {
                    destinationUrl =
                        cinema.maps_url;

                    buttonText =
                        'Open in Google Maps';

                    externalAttributes =
                        'target="_blank" rel="noopener noreferrer"';
                }

                const safeDestinationUrl =
                    destinationUrl
                        ? escapeHtml(
                            destinationUrl
                        )
                        : null;

                const cinemaContent = `
                    ${
                        isSelected
                            ? `
                                <div class="mb-2">
                                    <span class="badge bg-dark text-warning">
                                        <i class="fa-solid fa-check me-1"></i>
                                        SELECTED
                                    </span>
                                </div>
                            `
                            : ''
                    }

                    <div class="cinema-card-name">
                        ${cinemaName}
                    </div>

                    <div class="cinema-card-meta">
                        ${cinemaAddress}
                    </div>

                    ${distance}
                `;

                const cinemaMainLink =
                    safeDestinationUrl
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

                const websiteButton =
                    safeDestinationUrl
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
                        <div
                            class="cinema-card ${
                                isSelected
                                    ? 'cinema-card-selected'
                                    : ''
                            }"
                        >
                            ${cinemaMainLink}

                            ${websiteButton}
                        </div>
                    </div>
                `;
            })
            .join('');

        /**
         * Use exactly the same cinema data
         * for the Home Mini Map.
         */
        renderMiniMap(
            cinemas,
            userLatitude,
            userLongitude
        );
    }

    /**
     * Fetch nearby cinemas.
     */
    function fetchCinemas(
        lat = null,
        lng = null
    ) {
        showStatus(
            'Loading nearby theaters...',
            true
        );

        const params =
            new URLSearchParams();

        if (
            lat !== null &&
            lng !== null
        ) {
            params.set(
                'lat',
                lat
            );

            params.set(
                'lng',
                lng
            );
        }

        const queryString =
            params.toString();

        const url =
            queryString
                ? `/api/nearby-cinemas?${queryString}`
                : '/api/nearby-cinemas';

        fetch(url, {
            headers: {
                Accept:
                    'application/json',
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
            .then((payload) => {
                renderCinemas(
                    payload,
                    lat,
                    lng
                );
            })
            .catch((error) => {
                console.error(error);

                showStatus(
                    'Could not load theaters right now.'
                );
            });
    }

    /**
     * Use database fallback.
     */
    function fetchFallback() {
        fetchCinemas();
    }

    /**
     * Request browser location.
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
     * Handle location permission choice.
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
     * Permission buttons.
     */
    document
        .querySelectorAll(
            '[data-choice]'
        )
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
     * Change location preference.
     */
    changeBtn.addEventListener(
        'click',
        () => {
            showOverlay();
        }
    );

    /**
     * Restore saved preference.
     */
    const storedPreference =
        localStorage.getItem(
            STORAGE_KEY
        );

    if (
        storedPreference ===
        'always'
    ) {
        requestLocationAndFetch();

    } else if (
        storedPreference ===
        'deny'
    ) {
        fetchFallback();

    } else {
        showOverlay();
    }
})();