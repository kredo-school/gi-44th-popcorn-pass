document.addEventListener('DOMContentLoaded', () => {

    const movieId = document.querySelector('#movie-id')?.value;
    const generateUrl = document.querySelector('#generate-url')?.value;
    const showtimesUrl = document.querySelector('#showtimes-url')?.value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;


    function loadShowtimes() {

        document.querySelector('#showtime-list-container').innerHTML =
            '<div class="text-secondary text-center py-3">Loading...</div>';

        fetch(showtimesUrl)
            .then(r => r.json())
            .then(data => {

                // ここから今の処理をそのまま移動

            });

    }


    document.querySelector('#showtimes-tab-btn')
        ?.addEventListener('click', loadShowtimes);


    document.querySelector('#refresh-btn')
        ?.addEventListener('click', loadShowtimes);


});