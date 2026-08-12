// ==========================
// Movie Edit Page
// ==========================

document.addEventListener('DOMContentLoaded', () => {
    const releaseDate =
        document.querySelector('#released-date');

    const endDate =
        document.querySelector('#end-date');

    const statusBadge =
        document.querySelector('#movie-status-badge');

    function updateMovieStatus() {
        if (!releaseDate || !endDate || !statusBadge) {
            return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const release = releaseDate.value
            ? new Date(`${releaseDate.value}T00:00:00`)
            : null;

        const end = endDate.value
            ? new Date(`${endDate.value}T00:00:00`)
            : null;

        let status;
        let className;

        if (end && end < today) {
            status = 'Archived';
            className = 'bg-secondary';
        } else if (release && release <= today) {
            status = 'Now Showing';
            className = 'bg-success';
        } else {
            status = 'Coming Soon';
            className = 'bg-warning text-dark';
        }

        statusBadge.textContent = status;
        statusBadge.className =
            `badge p-2 ${className}`;
    }

    releaseDate?.addEventListener(
        'change',
        updateMovieStatus
    );

    endDate?.addEventListener(
        'change',
        updateMovieStatus
    );

    updateMovieStatus();
});