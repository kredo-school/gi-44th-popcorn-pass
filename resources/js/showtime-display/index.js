document.addEventListener('DOMContentLoaded', () => {
    const nextButton = document.getElementById('dateSliderNextBtn');
    const dateSlider = document.getElementById('dateSlider');

    // Only run on the Showtime Display page.
    if (!nextButton || !dateSlider) return;

    nextButton.addEventListener('click', () => {
        dateSlider.scrollBy({
            left: 200,
            behavior: 'smooth',
        });
    });
});