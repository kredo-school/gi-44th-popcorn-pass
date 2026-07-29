document.addEventListener('DOMContentLoaded', () => {

    const buttons = document.querySelectorAll('.payment-btn');
    const nextButton = document.getElementById('next-btn');
    const paymentMethodInput = document.getElementById('payment_method');

    if (buttons.length === 0) return;

    const forms = {
        paypal: document.getElementById('paypal-form'),
        onsite: document.getElementById('onsite-form'),
    };

    let selectedMethod = '';

    nextButton.disabled = true;

    Object.values(forms).forEach(form => {
        if (form) {
            form.classList.add('d-none');
        }
    });

    buttons.forEach(button => {
        button.addEventListener('click', () => {

            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            Object.values(forms).forEach(form => {
                if (form) {
                    form.classList.add('d-none');
                }
            });

            selectedMethod = button.dataset.method;

            if (forms[selectedMethod]) {
                forms[selectedMethod].classList.remove('d-none');
            }

            paymentMethodInput.value = selectedMethod;

            updateNextButton();
        });
    });

    function updateNextButton() {

        if (!selectedMethod) {
            nextButton.disabled = true;
            return;
        }

        if (selectedMethod === 'onsite') {
            nextButton.disabled = false;

        } else if (selectedMethod === 'paypal') {
            const email = document.querySelector('#paypal-form input[type="email"]');
            nextButton.disabled = email.value.trim() === '';
        }
    }

    const paypalEmail = document.querySelector('#paypal-form input[type="email"]');

    if (paypalEmail) {
        paypalEmail.addEventListener('input', updateNextButton);
    }

});