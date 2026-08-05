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


    movieRows.forEach(row => {


        row.addEventListener('click', function(){


            // data-movie-id を取得
            const movieId = this.dataset.movieId;


            window.selectedMovieId = movieId;


            console.log(
                'selected movie:',
                window.selectedMovieId
            );



            // Edit button

            const editBtn =
                document.querySelector('#edit-movie-btn');


            if(editBtn){

                editBtn.classList.remove('disabled');

                editBtn.href =
                    `/admin/movies/${movieId}/edit`;

            }



            // Archive button

            const archiveBtn =
                document.querySelector('#archive-movie-btn');


            if(archiveBtn){

                archiveBtn.classList.remove('disabled');

            }


        });


    });


});