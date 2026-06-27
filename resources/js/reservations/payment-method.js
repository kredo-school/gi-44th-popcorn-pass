document.addEventListener('DOMContentLoaded', () => {

    const buttons = document.querySelectorAll('.payment-btn');

    if (buttons.length === 0) {
        return;
    }

    const forms = {
        'Credit Card': document.getElementById('card-form'),
        'Paypal': document.getElementById('paypal-form'),
        'Pay On-Site': document.getElementById('onsite-form'),
    };

    buttons.forEach(button => {

        button.addEventListener('click', () => {
            buttons.forEach(btn => btn.classList.remove('active'));

            button.classList.add('active');

            Object.values(forms).forEach(form => {
                form.classList.add('d-none');
            });

            forms[button.textContent.trim()].classList.remove('d-none');
        });

    });

});