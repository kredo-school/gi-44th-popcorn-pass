document.addEventListener('DOMContentLoaded', function () {

    const seats = document.querySelectorAll('.seat');
    const summary = document.getElementById('selected-seats');

    let selectedSeats = [];

    function updateSummary() {
        if (selectedSeats.length === 0) {
            summary.innerHTML = "<p>No seats selected</p>";
            return;
        }

        summary.innerHTML = selectedSeats
            .map(seat => `<span class="seat-tag bg-white text-black me-1">${seat}</span>`)
            .join('');
    }

    seats.forEach(seat => {
        seat.addEventListener('click', function () {

            const seatNumber = this.dataset.seat;

            this.classList.toggle('selected');

            if (selectedSeats.includes(seatNumber)) {
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
            } else {
                selectedSeats.push(seatNumber);
            }

            updateSummary();
        });
    });

});