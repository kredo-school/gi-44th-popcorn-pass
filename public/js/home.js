// public/js/home.js
(function () {
    // ===========================
    // Horizontal slider buttons (Now Playing / Coming Soon)
    // ===========================
    function bindSlider(buttonId, targetId, distance) {
        const button = document.getElementById(buttonId);
        const track = document.getElementById(targetId);

        if (!button || !track) {
            return;
        }

        button.addEventListener('click', function () {
            track.scrollBy({ left: distance, behavior: 'smooth' });
        });
    }

    bindSlider('nowPlayingPrevBtn', 'nowPlayingSlider', -280);
    bindSlider('nowPlayingNextBtn', 'nowPlayingSlider', 280);
    bindSlider('comingSoonNextBtn', 'comingSoonSlider', 280);

    // ===========================
    // Location permission + nearby cinemas
    // ===========================
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

    function showStatus(message, showSpinner) {
        statusEl.classList.remove('is-hidden');
        listEl.classList.add('is-hidden');
        statusEl.innerHTML = (showSpinner ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>' : '') + message;
    }

    function renderCinemas(payload) {
        const cinemas = (payload && payload.cinemas) ? payload.cinemas : [];

        if (cinemas.length === 0) {
            showStatus('No cinemas found nearby.', false);
            return;
        }

        statusEl.classList.add('is-hidden');
        listEl.classList.remove('is-hidden');
        listEl.innerHTML = cinemas.map(function (cinema) {
            const distance = cinema.distance_km !== null && cinema.distance_km !== undefined
                ? '<div class="cinema-card-distance mb-2">' + cinema.distance_km + ' km away</div>'
                : '';
            const websiteBtn = cinema.maps_url
                ? '<a href="' + cinema.maps_url + '" target="_blank" rel="noopener" class="btn btn-sm location-btn location-btn-primary w-100 mt-2">Visit Website</a>'
                : '';

            return '' +
                '<div class="col-md-4 col-lg-3">' +
                    '<div class="cinema-card">' +
                        '<div class="cinema-card-name">' + escapeHtml(cinema.name) + '</div>' +
                        '<div class="cinema-card-meta">' + escapeHtml(cinema.address || '') + '</div>' +
                        distance +
                        websiteBtn +
                    '</div>' +
                '</div>';
        }).join('');
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function fetchCinemas(lat, lng) {
        showStatus('Loading nearby theaters...', true);

        const params = new URLSearchParams();
        if (lat !== null && lng !== null) {
            params.set('lat', lat);
            params.set('lng', lng);
        }

        fetch('/api/nearby-cinemas?' + params.toString())
            .then(function (res) { return res.json(); })
            .then(renderCinemas)
            .catch(function () {
                showStatus('Could not load theaters right now.', false);
            });
    }

    function fetchFallback() {
        fetchCinemas(null, null);
    }

    function requestLocationAndFetch() {
        if (!navigator.geolocation) {
            fetchFallback();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                fetchCinemas(position.coords.latitude, position.coords.longitude);
            },
            function () {
                // Permission denied at browser level, or unavailable — fall back.
                fetchFallback();
            },
            { timeout: 8000 }
        );
    }

    function handleChoice(choice) {
        if (choice === 'always') {
            localStorage.setItem(STORAGE_KEY, 'always');
            hideOverlay();
            requestLocationAndFetch();
        } else if (choice === 'once') {
            localStorage.removeItem(STORAGE_KEY);
            hideOverlay();
            requestLocationAndFetch();
        } else if (choice === 'deny') {
            localStorage.setItem(STORAGE_KEY, 'deny');
            hideOverlay();
            fetchFallback();
        }
    }

    document.querySelectorAll('[data-choice]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            handleChoice(btn.getAttribute('data-choice'));
        });
    });

    changeBtn.addEventListener('click', function () {
        showOverlay();
    });

    const storedPref = localStorage.getItem(STORAGE_KEY);

    if (storedPref === 'always') {
        requestLocationAndFetch();
    } else if (storedPref === 'deny') {
        fetchFallback();
    } else {
        showOverlay();
    }
})();