document.querySelectorAll('.showtime-reservation-btn')
    .forEach(button => {

        button.addEventListener('click', function () {

            document.getElementById('selectedShowtimeId').value =
                this.dataset.showtimeId;

        });

    });