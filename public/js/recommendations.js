document.addEventListener('DOMContentLoaded', function () {
    loadRecommendations();
});

function loadRecommendations() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/api/recommendations', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
        },
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.recommendations.length > 0) {
                renderRecommendations(data.recommendations);
            } else {
                renderNoRecommendations();
            }
        })
        .catch(error => {
            console.error('Error loading recommendations:', error);
            renderError();
        });
}

function renderRecommendations(recommendations) {
    const container = document.getElementById('recommendedContainer');
    const mypageContainer = document.getElementById('mypageRecommendedContainer');

    const html = recommendations.map(movie => `
        <div class="col-md-4 mb-4">
            <div class="card recommendation-card h-100">
                <img src="${movie.poster_url}" class="card-img-top" alt="${movie.title}">
                <div class="card-body">
                    <h5 class="card-title">${movie.title}</h5>
                    <div class="rating mb-2">
                        <span class="stars">${renderStars(movie.review_average)}</span>
                        <span class="rating-value">${(movie.review_average / 2).toFixed(1)}/5</span>
                    </div>
                    <p class="card-text text-muted small">Match: ${(movie.recommendation_score * 20).toFixed(0)}%</p>
                    <a href="/movies/${movie.id}" class="btn btn-primary btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
    `).join('');

    if (container) {
        container.innerHTML = html;
    }
    if (mypageContainer) {
        mypageContainer.innerHTML = html;
    }
}

function renderNoRecommendations() {
    const container = document.getElementById('recommendedContainer');
    if (container) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-muted">No recommendations available yet. Watch more movies to get personalized recommendations!</p>
            </div>
        `;
    }
}

function renderError() {
    const container = document.getElementById('recommendedContainer');
    if (container) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-danger">Error loading recommendations. Please try again later.</p>
            </div>
        `;
    }
}

function renderStars(rating) {
    // rating は 0-10 scale なので、0-5 に正規化
    const normalizedRating = rating / 2;
    const fullStars = Math.floor(normalizedRating);
    const hasHalfStar = normalizedRating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    let stars = '★'.repeat(fullStars);
    if (hasHalfStar) stars += '☆';
    stars += '☆'.repeat(emptyStars);

    return stars;
}