document.addEventListener('DOMContentLoaded', function () {

    const seats = document.querySelectorAll('.seat');
    const summary = document.getElementById('selected-seats');
    const hiddenInput = document.getElementById('selectedSeatsInput');
    const nextButton = document.querySelector('.next-btn');
    const limitMsg = document.getElementById('seat-limit-msg');

    if (!hiddenInput || !summary || !nextButton) {
        return;
    }

    let selectedSeats = [];

    const dataEl = document.getElementById('seat-data');
    if (dataEl) {
        const savedSeats = JSON.parse(dataEl.dataset.seats || '[]');
        savedSeats.forEach(saved => {
            const seatBtn = document.querySelector(`[data-seat="${saved.seat}"]`);
            if (seatBtn) {
                seatBtn.classList.add('selected');
                selectedSeats.push(saved);
            }
        });
    }

    function updateSummary() {

        hiddenInput.value = JSON.stringify(selectedSeats);

        nextButton.disabled = selectedSeats.length === 0;

        if (selectedSeats.length === 0) {
            summary.innerHTML = "No seats selected";
            return;
        }

        summary.innerHTML = selectedSeats.map(item => `
            <span class="seat-tag ${item.premium ? 'premium' : 'normal'}">
                ${item.seat}
            </span>
        `).join('');
    }

    seats.forEach(seat => {

        seat.addEventListener('click', function () {

            const seatNumber = this.dataset.seat;
            const isPremium = this.classList.contains('premium');

            const existingIndex = selectedSeats.findIndex(
                s => s.seat === seatNumber
            );

            if (existingIndex !== -1) {
                selectedSeats.splice(existingIndex, 1);
                this.classList.remove('selected');
                limitMsg.style.display = 'none';
            } else {
                if (selectedSeats.length >= 6) {
                    limitMsg.style.display = 'block';
                    return;
                }
                limitMsg.style.display = 'none';
                selectedSeats.push({
                    seat: seatNumber,
                    premium: isPremium
                });

                this.classList.add('selected');
            }

            updateSummary();
        });

    });

    updateSummary();
});