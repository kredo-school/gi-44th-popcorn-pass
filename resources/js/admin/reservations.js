document.addEventListener('DOMContentLoaded', () => {
    const reservationRows = document.querySelectorAll('.reservation-row');
    const paymentAction = document.getElementById('detail-payment-action');
    const markPaidButton = document.getElementById('mark-payment-paid-btn');

    if (reservationRows.length === 0) return;

    reservationRows.forEach(row => {
        row.addEventListener('click', async () => {
            const reservationId = row.dataset.reservationId;

            try {
                const response = await fetch(`/admin/reservations/${reservationId}/details`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load reservation details.');
                }

                const data = await response.json();

                displayReservationDetails(data);
                updatePaymentAction(data);
            } catch (error) {
                console.error(error);
                alert('Reservation details could not be loaded.');
            }
        });
    });

    if (markPaidButton && paymentAction) {
        markPaidButton.addEventListener('click', () => {
            const paymentId = markPaidButton.dataset.paymentId;

            if (!paymentId) return;

            if (!window.confirm('Mark this on-site payment as paid?')) {
                return;
            }

            submitMarkPaidForm(paymentId);
        });
    }

    function displayReservationDetails(data) {
        setText('#detail-booking-id', data.reservation_reference);

        setText(
            '#detail-customer',
            `${data.customer_name ?? '—'} (${data.customer_email ?? '—'})`
        );

        setText(
            '#detail-movie-cinema',
            `${data.movie_title ?? '—'} / ${data.cinema_name ?? '—'} / Screen ${data.screen_number ?? '—'}`
        );

        setText('#detail-showtime', data.showtime);

        const seats = Array.isArray(data.seats) && data.seats.length > 0
            ? data.seats.join(', ')
            : '—';

        setText('#detail-seats', seats);

        setText(
            '#detail-amount',
            `$${data.subtotal ?? '0.00'} / -$${data.discount_amount ?? '0.00'} / $${data.final_amount ?? '0.00'}`
        );

        setText(
            '#detail-payment',
            `${data.payment_status ?? '—'} (${data.payment_method ?? '—'}) - ${data.transaction_id ?? '—'}`
        );

        setText('#detail-status', data.reservation_status);
        setText('#detail-qr', data.qr_code);
    }

    function updatePaymentAction(data) {
        if (!paymentAction || !markPaidButton) return;

        if (data.can_mark_paid && data.payment_id) {
            markPaidButton.dataset.paymentId = data.payment_id;
            paymentAction.classList.remove('d-none');
        } else {
            delete markPaidButton.dataset.paymentId;
            paymentAction.classList.add('d-none');
        }
    }

    function submitMarkPaidForm(paymentId) {
        const urlTemplate = paymentAction.dataset.markPaidUrlTemplate;
        const csrfToken = paymentAction.dataset.csrfToken;

        if (!urlTemplate || !csrfToken) {
            alert('The payment update configuration is missing.');
            return;
        }

        const form = document.createElement('form');

        form.method = 'POST';
        form.action = urlTemplate.replace(
            '__PAYMENT_ID__',
            encodeURIComponent(paymentId)
        );

        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="PATCH">
        `;

        document.body.appendChild(form);
        form.submit();
    }

    function setText(selector, value) {
        const element = document.querySelector(selector);

        if (element) {
            element.textContent = value || '—';
        }
    }
});