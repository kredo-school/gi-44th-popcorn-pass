document.addEventListener('DOMContentLoaded', () => {
    const dateSlider = document.getElementById('dateSlider-selection');

    if (!dateSlider) return;

    const nextButton = document.querySelector('.slider-next');

    nextButton?.addEventListener('click', () => {
        dateSlider.scrollBy({
            left: 200,
            behavior: 'smooth'
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {

    const reservationButtons = document.querySelectorAll('.showtime-reservation-btn');
    const showtimeInput = document.getElementById('selectedShowtimeId');
    const loginButton = document.getElementById('loginButton');

    reservationButtons.forEach(button => {
        button.addEventListener('click', () => {
            showtimeInput.value = button.dataset.showtimeId;
        });
    });

    loginButton?.addEventListener('click', () => {
        const showtimeId = showtimeInput.value;

        window.location.href =
            `/reservations/login-redirect?showtime_id=${showtimeId}`;
    });

});