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


    function updateTotal() {

        let total = selectedSeats.reduce((sum, seat) => {

            let price = Number(seat.price) || 0;

            if (seat.premium) {
                price += 10;
            }

            return sum + price;

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
            const price = Number(this.dataset.price);

            if (!currentButton) return;

            currentButton.innerHTML = `
                <span class="ticket-name">${ticketName}</span>
                <span class="ticket-price">$${price}</span>
            `;

            currentButton.classList.add('selected');

            currentButton.dataset.ticket = ticketName;
            currentButton.dataset.price = price;

            const seatCard = currentButton.closest('.ticket-card');

            const seatNumber = seatCard
                .querySelector('.seat-number-box')
                .textContent
                .trim();

            const seat = selectedSeats.find(s => s.seat === seatNumber);

            if (seat) {
                seat.ticket = ticketName;
                seat.price = price;
            }

            updateTotal();
            checkAllSelected();

            console.log('selectedSeats:', selectedSeats);
        });
    });


    updateTotal();
    checkAllSelected();

    nextButton.addEventListener('click', function () {

    fetch('/reservation/save-ticket', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            seats: selectedSeats
        })
    }).then(() => {
        window.location.href = "{{ route('reservations.payment-method') }}";
    });

});

});