/**
 * Recommendations Module
 * Handles fetching and rendering personalized movie recommendations
 */

(function () {
    'use strict';

    function fetchRecommendations() {
        const container = document.getElementById('recommendations-container');
        
        if (!container) {
            return;
        }

        fetch('/api/recommendations?limit=5')
            .then(response => {
                if (!response.ok) {
                    throw new Error('API Error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                renderRecommendations(container, data);
            })
            .catch(error => {
                console.error('Error fetching recommendations:', error);
                renderError(container);
            });
    }

    function renderRecommendations(container, data) {
        container.innerHTML = '';

        if (data.data && data.data.length > 0) {
            const html = data.data.map(movie => `
                <div class="col-md-4 col-lg-2">
                    <div class="card recommendation-card h-100">
                        <img src="${movie.poster_url || '/images/no-poster.png'}" 
                             alt="${movie.title}"
                             class="card-img-top">
                        <div class="card-body">
                            <h6 class="card-title">${movie.title}</h6>
                        </div>
                        <div class="recommendation-score">
                            ${Math.round(movie.recommendation_score * 10) / 10}⭐
                        </div>
                    </div>
                </div>
            `).join('');
            container.innerHTML = html;
        } else {
            renderEmpty(container);
        }
    }

    function renderEmpty(container) {
        container.innerHTML = `
            <div class="col-12 recommendations-empty">
                <i class="fa-solid fa-film"></i>
                <p>No recommendations available yet.<br>Watch more movies to get personalized recommendations!</p>
            </div>
        `;
    }

    function renderError(container) {
        container.innerHTML = `
            <div class="col-12 recommendations-empty">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p class="text-danger">Failed to load recommendations</p>
            </div>
        `;
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', fetchRecommendations);
})();