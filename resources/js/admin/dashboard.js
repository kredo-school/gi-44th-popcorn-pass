document.addEventListener('DOMContentLoaded', () => {

    const editButton = document.querySelector('#edit-movie-btn');

    document.querySelectorAll('.movie-row').forEach(function(row) {

        row.addEventListener('click', function() {

            const movieId = this.dataset.movieId;

            window.selectedMovieId = movieId;

            console.log('selected movie:', window.selectedMovieId);


            // Edit
            if (editButton) {
                editButton.href = `/admin/movies/${movieId}/edit`;
                editButton.classList.remove('disabled');
            }


            // Archive
            const archiveButton = document.querySelector('#archive-movie-btn');

            if (archiveButton) {
                archiveButton.classList.remove('disabled');
            }


            fetch(`/admin/movies/${movieId}/details`)
                .then(response => response.json())
                .then(data => {


                    const titleElement =
                        document.querySelector('#detail-title');

                    if (titleElement) {
                        titleElement.textContent = data.title || '—';
                    }


                    const synopsisElement =
                        document.querySelector('#detail-synopsis');

                    if (synopsisElement) {
                        synopsisElement.textContent =
                            data.synopsis || '—';
                    }


                    const directorElement =
                        document.querySelector('#detail-director');

                    if (directorElement) {
                        directorElement.textContent =
                            data.director || '—';
                    }



                    // Cast
                    const castElement =
                        document.querySelector('#detail-cast');


                    if (castElement) {

                        if (data.cast) {

                            let casts = data.cast;

                            if (typeof casts === 'string') {
                                try {
                                    casts = JSON.parse(casts);
                                } catch (e) {
                                    casts = [casts];
                                }
                            }


                            castElement.innerHTML =
                                casts.map(cast => `
                                    <div class="mb-2 text-start">
                                        ${cast}
                                    </div>
                                `).join('');

                        } else {

                            castElement.textContent = '—';

                        }
                    }



                    // Genre
                    // Genre

const genreElement =
    document.querySelector('#detail-genre');


if (genreElement) {

    if (data.genres && data.genres.length > 0) {


        const genreColors = {

            'Action': 'genre-action',
            'Adventure': 'genre-adventure',
            'Animation': 'genre-animation',
            'Comedy': 'genre-comedy',
            'Crime': 'genre-crime',
            'Drama': 'genre-drama',
            'Fantasy': 'genre-fantasy',
            'Horror': 'genre-horror',
            'Mystery': 'genre-mystery',
            'Romance': 'genre-romance',
            'Sci-Fi': 'genre-scifi',
            'Thriller': 'genre-thriller',

        };


        genreElement.innerHTML =
            data.genres.map(genre => `

                <span class="badge genre-badge ${genreColors[genre] ?? ''}">
                    ${genre}
                </span>

            `).join('');

    } else {

        genreElement.textContent = '—';

    }

}



                    const trailerElement =
                        document.querySelector('#detail-trailer');


                    if (trailerElement) {

                        trailerElement.textContent =
                            data.trailer_url || '—';

                    }


                });

        });

    });

});