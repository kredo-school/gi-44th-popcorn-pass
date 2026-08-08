document.addEventListener('DOMContentLoaded', () => {
    const movieRows = document.querySelectorAll('.movie-row');
    const editButton = document.querySelector('#edit-movie-btn');

    // Only run on the Movies index page.
    if (movieRows.length === 0) {
        return;
    }

    const detailTitle =
        document.querySelector('#detail-title');

    const detailSynopsis =
        document.querySelector('#detail-synopsis');

    const detailDirector =
        document.querySelector('#detail-director');

    const detailCast =
        document.querySelector('#detail-cast');

    const detailTrailer =
        document.querySelector('#detail-trailer');

    movieRows.forEach((row) => {
        row.addEventListener('click', async () => {
            const movieId = row.dataset.movieId;

            if (!movieId) {
                return;
            }

            if (editButton) {
                editButton.href =
                    `/admin/movies/${movieId}/edit`;

                editButton.classList.remove('disabled');
            }

            try {
                const response = await fetch(
                    `/admin/movies/${movieId}/details`,
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        `Failed to load movie details: ${response.status}`
                    );
                }

                const data = await response.json();

                if (detailTitle) {
                    detailTitle.textContent =
                        data.title || '—';
                }

                if (detailSynopsis) {
                    detailSynopsis.textContent =
                        data.synopsis || '—';
                }

                if (detailDirector) {
                    detailDirector.textContent =
                        data.director || '—';
                }

                if (detailCast) {
                    detailCast.textContent =
                        Array.isArray(data.cast) &&
                        data.cast.length > 0
                            ? data.cast.join(', ')
                            : '—';
                }

                if (detailTrailer) {
                    detailTrailer.textContent =
                        data.trailer_url || '—';
                }
            } catch (error) {
                console.error(error);
            }
        });
    });
});