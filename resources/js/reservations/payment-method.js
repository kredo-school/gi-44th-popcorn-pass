document.addEventListener('DOMContentLoaded', () => {

    const buttons = document.querySelectorAll('.payment-btn');

    if (buttons.length === 0) return;

    const forms = {
        card: document.getElementById('card-form'),
        paypal: document.getElementById('paypal-form'),
        onsite: document.getElementById('onsite-form'),
    };

    buttons.forEach(button => {

        button.addEventListener('click', () => {

            buttons.forEach(btn => btn.classList.remove('active'));

            button.classList.add('active');

            Object.values(forms).forEach(form => {
                form.classList.add('d-none');
            });

            const method = button.dataset.method;

            forms[method].classList.remove('d-none');
        });

    });

});