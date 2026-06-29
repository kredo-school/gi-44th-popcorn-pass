document.addEventListener('DOMContentLoaded', () => {

    const buttons = document.querySelectorAll('.payment-btn');
    const nextButton = document.getElementById('next-btn');

    if (buttons.length === 0) return;

    const forms = {
        card: document.getElementById('card-form'),
        paypal: document.getElementById('paypal-form'),
        onsite: document.getElementById('onsite-form'),
    };

    let selectedMethod = 'card';

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            Object.values(forms).forEach(form => form.classList.add('d-none'));

            selectedMethod = button.dataset.method;
            forms[selectedMethod].classList.remove('d-none');

            updateNextButton();
        });
    });

    function updateNextButton() {
        if (selectedMethod === 'onsite') {
            nextButton.disabled = false;
        } else if (selectedMethod === 'card') {
            const cardNumber = document.querySelector('#card-form input[placeholder="Card Number"]');
            nextButton.disabled = cardNumber.value.length < 4;
        } else if (selectedMethod === 'paypal') {
            const email = document.querySelector('#paypal-form input[type="email"]');
            nextButton.disabled = email.value.trim() === '';
        }
    }

    document.querySelector('#card-form input[placeholder="Card Number"]')
        .addEventListener('input', updateNextButton);
    document.querySelector('#paypal-form input[type="email"]')
        .addEventListener('input', updateNextButton);

    updateNextButton();

    nextButton.addEventListener('click', function () {
        let paymentData = { method: selectedMethod, last4: null };

        if (selectedMethod === 'card') {
            const cardNumber = document.querySelector('#card-form input[placeholder="Card Number"]').value;
            paymentData.last4 = cardNumber.slice(-4);
        } else if (selectedMethod === 'paypal') {
            paymentData.email = document.querySelector('#paypal-form input[type="email"]').value;
        }

        fetch('/save-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(paymentData)
        }).then(() => {
            window.location.href = '/reservation-confirm';
        });
    });

});