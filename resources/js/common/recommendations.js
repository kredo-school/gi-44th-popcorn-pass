/**
 * Recommendations Module
 * Handles fetching and rendering personalized movie recommendations
 */

(function () {
    'use strict';

    const container = document.getElementById(
        'recommendations-container'
    );

    // Recommendedセクションがないページでは何もしない
    if (!container) {
        return;
    }

    function escapeHtml(value) {
        const element = document.createElement('div');

        element.textContent = value ?? '';

        return element.innerHTML;
    }

    function getPosterUrl(posterUrl) {
        if (!posterUrl) {
            return '/images/no-poster.png';
        }

        try {
            const url = new URL(posterUrl, window.location.origin);

            if (!['http:', 'https:'].includes(url.protocol)) {
                return '/images/no-poster.png';
            }

            return url.href;
        } catch {
            return '/images/no-poster.png';
        }
    }

    function formatScore(score) {
        const number = Number(score);

        if (!Number.isFinite(number)) {
            return null;
        }

        return Math.round(number * 10) / 10;
    }

    function fetchRecommendations() {
        fetch('/api/recommendations?limit=5', {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `Recommendations API error: ${response.status}`
                    );
                }

                return response.json();
            })
            .then((data) => {
                renderRecommendations(data);
            })
            .catch((error) => {
                console.error(
                    'Error fetching recommendations:',
                    error
                );

                renderError();
            });
    }

    function renderRecommendations(data) {
        const movies = Array.isArray(data?.data)
            ? data.data
            : [];

        if (movies.length === 0) {
            renderEmpty();
            return;
        }

        container.innerHTML = movies
            .map((movie) => {
                const title = escapeHtml(movie.title);
                const posterUrl = escapeHtml(
                    getPosterUrl(movie.poster_url)
                );
                const score = formatScore(
                    movie.recommendation_score
                );

                const scoreHtml = score !== null
                    ? `
                        <div class="recommendation-score">
                            ${score} ⭐
                        </div>
                    `
                    : '';

                return `
                    <div class="col-md-4 col-lg-2">
                        <div class="card recommendation-card h-100">
                            <img
                                src="${posterUrl}"
                                alt="${title}"
                                class="card-img-top"
                            >

                            <div class="card-body">
                                <h6 class="card-title">
                                    ${title}
                                </h6>
                            </div>

                            ${scoreHtml}
                        </div>
                    </div>
                `;
            })
            .join('');
    }

    function renderEmpty() {
        container.innerHTML = `
            <div class="col-12 recommendations-empty">
                <i class="fa-solid fa-film"></i>

                <p>
                    No recommendations available yet.<br>
                    Watch more movies to get personalised recommendations!
                </p>
            </div>
        `;
    }

    function renderError() {
        container.innerHTML = `
            <div class="col-12 recommendations-empty">
                <i class="fa-solid fa-triangle-exclamation"></i>

                <p class="text-danger">
                    Failed to load recommendations.
                </p>
            </div>
        `;
    }

    fetchRecommendations();
})();