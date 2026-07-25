document.addEventListener('DOMContentLoaded', () => {

    const editButton = document.querySelector('#edit-movie-btn');

    document.querySelectorAll('.movie-row').forEach(function(row) {
        row.addEventListener('click', function() {

            const movieId = this.dataset.movieId;

            if (editButton) {
                editButton.href = `/admin/movies/${movieId}/edit`;
                editButton.classList.remove('disabled');
            }

            fetch(`/admin/movies/${movieId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#detail-title').textContent = data.title || '—';
                    document.querySelector('#detail-synopsis').textContent = data.synopsis || '—';
                    document.querySelector('#detail-director').textContent = data.director || '—';
                    document.querySelector('#detail-cast').textContent = data.cast || '—';
                    document.querySelector('#detail-trailer').textContent = data.trailer_url || '—';
                });

        });
    });

});