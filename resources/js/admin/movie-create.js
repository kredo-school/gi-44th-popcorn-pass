document.addEventListener('DOMContentLoaded', () => {

    const releaseDate =
        document.querySelector('#released-date');

    const endDate =
        document.querySelector('#end-date');

    const statusBadge =
        document.querySelector('#movie-status-badge');


    function updateMovieStatus() {

        const today = new Date();
        today.setHours(0,0,0,0);


        const release =
            releaseDate.value
                ? new Date(releaseDate.value)
                : null;

        const end =
            endDate.value
                ? new Date(endDate.value)
                : null;


        let status = 'Select dates';
        let className = 'bg-secondary';


        if (end && end < today) {

            status = 'Archived';
            className = 'bg-secondary';


        } else if (release && release <= today) {

            status = 'Now Showing';
            className = 'bg-success';


        } else if (release) {

            status = 'Coming Soon';
            className = 'bg-warning text-dark';

        }


        statusBadge.textContent = status;

        statusBadge.className =
            `badge p-2 ${className}`;

    }


    releaseDate.addEventListener(
        'change',
        updateMovieStatus
    );


    endDate.addEventListener(
        'change',
        updateMovieStatus
    );


    updateMovieStatus();

});