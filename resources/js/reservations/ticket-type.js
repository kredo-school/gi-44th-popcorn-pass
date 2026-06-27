document.addEventListener('DOMContentLoaded', function () {

    const ticketButtons = document.querySelectorAll('.ticket-type-btn');
    const ticketOptions = document.querySelectorAll('.ticket-option');
    const nextButton = document.getElementById('next-btn');

    if (ticketButtons.length === 0) return;

    let selectedSeats = [];

    const dataEl = document.getElementById('ticket-data');

    if (dataEl) {
        selectedSeats = JSON.parse(dataEl.dataset.seats || '[]');
    }

    let currentButton = null;
    let pendingSelection = null;

    function updateTotal() {
        let total = selectedSeats.reduce((sum, seat) => {
            let price = Number(seat.price) || 0;
            let premium = seat.premium ? 10 : 0;
            return sum + price + premium;
        }, 0);

        const el = document.querySelector('.total-price');
        if (el) {
            el.textContent = `$${total}`;
        }
    }

    function checkAllSelected() {
        const allSelected = selectedSeats.every(seat => {
            return seat.ticket && seat.price;
        });

        if (nextButton) {
            nextButton.disabled = !allSelected;
        }
    }

    ticketButtons.forEach(button => {
        button.addEventListener('click', function () {
            currentButton = this;
        });
    });

    ticketOptions.forEach(option => {
        option.addEventListener('click', function () {
            const ticketName = this.dataset.ticket;
            const basePrice = Number(this.dataset.price);

            if (!currentButton) return;

            pendingSelection = {
                button: currentButton,
                ticketName,
                basePrice,
                seatNumber: currentButton.dataset.seat
            };
        });
    });

    document.getElementById('ticketTypeModal').addEventListener('hidden.bs.modal', function () {
        if (!pendingSelection) return;

        const { button, ticketName, basePrice, seatNumber } = pendingSelection;

        button.innerHTML = `
            <span class="ticket-name">${ticketName}</span>
            <span class="ticket-price">$${basePrice}</span>
        `;
        button.classList.add('selected');

        const seat = selectedSeats.find(s => s.seat === seatNumber);
        if (seat) {
            seat.ticket = ticketName;
            seat.price = basePrice;
        }

        updateTotal();
        checkAllSelected();

        pendingSelection = null;
    });

    updateTotal();
    checkAllSelected();

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            fetch('/save-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    seats: selectedSeats
                })
            }).then(() => {
                window.location.href = "/payment-method";
            });
        });
    }

});