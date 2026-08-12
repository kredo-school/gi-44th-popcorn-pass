document.addEventListener('DOMContentLoaded', () => {

    // ==========================
    // Genre checkbox
    // ==========================

    const checkboxes = document.querySelectorAll('.genre-checkbox');

    checkboxes.forEach(checkbox => {

        checkbox.addEventListener('change', function () {

            const checked =
                document.querySelectorAll('.genre-checkbox:checked');

            if (checked.length > 3) {
                this.checked = false;
                alert('You can select up to 3 genres.');
            }

        });

    });


    // ==========================
    // Movie select
    // ==========================

    window.selectedMovieId = null;

    const movieRows = document.querySelectorAll('.movie-row');

    const editBtn = document.querySelector('#edit-movie-btn');
    const archiveBtn = document.querySelector('#archive-movie-btn');

    movieRows.forEach(row => {

        row.addEventListener('click', function () {

            
            const movieId = this.dataset.movieId;

            window.selectedMovieId = movieId;

            console.log('selected movie:', movieId);

            // Edit button
            if (editBtn) {
                editBtn.classList.remove('disabled');
                editBtn.href = `/admin/movies/${movieId}/edit`;
            }

            // Archive button
            if (archiveBtn) {
                archiveBtn.classList.remove('disabled');
                archiveBtn.dataset.movieId = movieId;
            }

        });

    });


    // ==========================
    // Archive Movie
    // ==========================

    if (archiveBtn) {

        archiveBtn.addEventListener('click', function (e) {

            e.preventDefault();

            if (this.classList.contains('disabled')) {
                return;
            }

            if (!confirm('Are you sure you want to archive this movie?')) {
                return;
            }

            const movieId = this.dataset.movieId;

            fetch(`/admin/movies/${movieId}/archive`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Archive failed.');
                }
                return response.json();
            })
            .then(() => {
                alert('Movie archived successfully.');
                location.reload();
            })
            .catch(error => {
                console.error(error);
                alert('Failed to archive the movie.');
            });

        });

    }

});
