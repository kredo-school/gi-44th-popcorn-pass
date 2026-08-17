(function () {
    'use strict';

    const FALLBACK_POSTER = '/images/no-poster.png';

    /**
     * Escape text inserted into HTML.
     */
    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /**
     * Convert a possible relative image path into a usable URL.
     */
    function normalizeImageUrl(path) {
        if (!path) {
            return FALLBACK_POSTER;
        }

        const imagePath = String(path).trim();

        if (!imagePath) {
            return FALLBACK_POSTER;
        }

        // Already a complete URL or data URI.
        if (
            imagePath.startsWith('http://') ||
            imagePath.startsWith('https://') ||
            imagePath.startsWith('data:')
        ) {
            return imagePath;
        }

        // Already an absolute application path.
        if (imagePath.startsWith('/')) {
            return imagePath;
        }

        // Files uploaded through Laravel's public storage.
        if (imagePath.startsWith('storage/')) {
            return `/${imagePath}`;
        }

        // Common database values such as movies/poster.jpg.
        return `/storage/${imagePath}`;
    }

    /**
     * Support both flattened API objects and:
     * {
     *   movie_id: ...,
     *   recommendation_score: ...,
     *   movie: {...}
     * }
     */
    function normalizeRecommendation(item) {
        const movie = item.movie ?? item;

        const movieId =
            item.movie_id ??
            movie.id ??
            null;

        const title =
            movie.title ??
            movie.movie_title ??
            movie.name ??
            'Untitled Movie';

        const posterPath =
            movie.poster_url ??
            movie.poster_path ??
            movie.poster ??
            movie.image_url ??
            movie.image_path ??
            movie.image ??
            movie.thumbnail ??
            null;

        const rawScore =
            item.recommendation_score ??
            movie.recommendation_score ??
            movie.average_rating ??
            movie.avg_rating ??
            movie.rating ??
            0;

        const score = Number.parseFloat(rawScore);

        return {
            id: movieId,
            title,
            posterUrl: normalizeImageUrl(posterPath),
            score: Number.isFinite(score) ? score : 0,
        };
    }

    async function fetchRecommendations() {
        const container = document.getElementById(
            'recommendations-container'
        );

        if (!container) {
            return;
        }

        renderLoading(container);

        try {
            const response = await fetch(
                '/api/recommendations?limit=5',
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }
            );

            if (!response.ok) {
                throw new Error(
                    `Recommendation API returned ${response.status}`
                );
            }

            const responseData = await response.json();

            renderRecommendations(container, responseData);
        } catch (error) {
            console.error(
                'Error fetching recommendations:',
                error
            );

            renderError(container);
        }
    }

    function getRecommendationItems(responseData) {
        // Supports:
        // { data: [...] }
        // { recommendations: [...] }
        // [...]
        if (Array.isArray(responseData)) {
            return responseData;
        }

        if (Array.isArray(responseData?.data)) {
            return responseData.data;
        }

        if (Array.isArray(responseData?.recommendations)) {
            return responseData.recommendations;
        }

        return [];
    }

    function renderRecommendations(container, responseData) {
        const items = getRecommendationItems(responseData);

        if (items.length === 0) {
            renderEmpty(container);
            return;
        }

        const html = items
            .map(normalizeRecommendation)
            .map(movie => createMovieCard(movie))
            .join('');

        container.innerHTML = html;
    }

    function createMovieCard(movie) {
        const title = escapeHtml(movie.title);
        const posterUrl = escapeHtml(movie.posterUrl);

        const scoreText =
            movie.score > 0
                ? `${movie.score.toFixed(1)} ★`
                : 'New';

        const movieUrl = movie.id
            ? `/movie/${encodeURIComponent(movie.id)}/detail`
            : '#';

        return `
            <div class="recommendation-column">
                <a
                    href="${movieUrl}"
                    class="recommendation-link text-decoration-none"
                    aria-label="View ${title}"
                >
                    <article class="card recommendation-card h-100">
                        <div class="recommendation-poster-wrapper">
                            <img
                                src="${posterUrl}"
                                alt="${title}"
                                class="card-img-top recommendation-poster"
                                loading="lazy"
                                onerror="
                                    this.onerror = null;
                                    this.src = '${FALLBACK_POSTER}';
                                "
                            >

                            <!--
                            <span class="recommendation-badge">
                                Recommended
                            </span>
                            -->

                            <span class="recommendation-score">
                                ${scoreText}
                            </span>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title mb-0">
                                ${title}
                            </h6>
                        </div>
                    </article>
                </a>
            </div>
        `;
    }

    function renderLoading(container) {
        container.innerHTML = Array.from(
            { length: 5 },
            () => `
                <div class="recommendation-column">
                    <div
                        class="card recommendation-card
                               recommendation-skeleton h-100"
                        aria-hidden="true"
                    >
                        <div class="skeleton-poster"></div>

                        <div class="card-body">
                            <div class="skeleton-title"></div>
                            <div class="skeleton-title short"></div>
                        </div>
                    </div>
                </div>
            `
        ).join('');
    }

    function renderEmpty(container) {
        container.innerHTML = `
            <div class="col-12 recommendations-empty text-center">
                <i class="fa-solid fa-film mb-3"></i>

                <p class="mb-1">
                    No recommendations available yet.
                </p>

                <small>
                    Watch and review more movies to improve your
                    recommendations.
                </small>
            </div>
        `;
    }

    function renderError(container) {
        container.innerHTML = `
            <div class="col-12 recommendations-empty text-center">
                <i
                    class="fa-solid fa-triangle-exclamation
                           text-danger mb-3"
                ></i>

                <p class="text-danger mb-2">
                    Failed to load recommendations.
                </p>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-warning"
                    id="retry-recommendations"
                >
                    Try again
                </button>
            </div>
        `;

        document
            .getElementById('retry-recommendations')
            ?.addEventListener(
                'click',
                fetchRecommendations,
                { once: true }
            );
    }

    document.addEventListener(
        'DOMContentLoaded',
        fetchRecommendations
    );
})();