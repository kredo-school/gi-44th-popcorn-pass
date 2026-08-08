const paypalCheckout = document.getElementById('paypal-checkout');

if (paypalCheckout) {
    initialisePayPalCheckout(paypalCheckout);
}

async function initialisePayPalCheckout(checkoutElement) {
    const clientId = checkoutElement.dataset.clientId;
    const currency = checkoutElement.dataset.currency;
    const createUrl = checkoutElement.dataset.createUrl;
    const captureUrl = checkoutElement.dataset.captureUrl;
    const formId = checkoutElement.dataset.formId;

    const errorBox = document.getElementById('paypal-error');
    const bookingForm = document.getElementById(formId);

    try {
        if (!clientId) {
            throw new Error('PayPal Client ID is not configured.');
        }

        await loadPayPalSdk(clientId, currency);

        window.paypal
    .Buttons({
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'rect',
            label: 'paypal',
        },

        createOrder: async () => {
            hideError(errorBox);

            const order =
                await postJson(createUrl);

            return order.id;
        },

        onApprove: async data => {
            hideError(errorBox);

            const result =
                await postJson(captureUrl, {
                    order_id: data.orderID,
                });

            if (result.status !== 'COMPLETED') {
                throw new Error(
                    'PayPal did not complete the payment.'
                );
            }

            bookingForm.submit();
        },

        onCancel: () => {
            showError(
                errorBox,
                'PayPal payment was cancelled. No booking was created.'
            );
        },

        onError: error => {
            console.error(error);

            showError(
                errorBox,
                error.message
                    ?? 'PayPal payment failed.'
            );
        },
    })
    .render('#paypal-button-container');
    } catch (error) {
        console.error(error);

        showError(
            errorBox,
            error.message
                ?? 'PayPal Checkout could not be loaded.'
        );
    }
}

function loadPayPalSdk(clientId, currency) {
    if (window.paypal) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement('script');

        const query = new URLSearchParams({
            'client-id': clientId,
            currency: currency,
            intent: 'capture',
            components: 'buttons',
        });

        script.src = `https://www.paypal.com/sdk/js?${query.toString()}`;
        script.onload = resolve;
        script.onerror = () => {
            reject(
                new Error(
                    'PayPal Checkout could not be loaded. Please refresh the page.'
                )
            );
        };

        document.head.appendChild(script);
    });
}

async function postJson(url, body = {}) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!csrfToken) {
        throw new Error('CSRF token was not found.');
    }

    const response = await fetch(url, {
        method: 'POST',

        credentials: 'same-origin',

        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },

        body: JSON.stringify(body),
    });

    const data = await response
        .json()
        .catch(() => ({}));

    if (!response.ok) {
        throw new Error(
            data.message
                ?? 'The payment request failed.'
        );
    }

    return data;
}

function showError(errorBox, message) {
    if (!errorBox) {
        return;
    }

    errorBox.textContent = message;
    errorBox.classList.remove('d-none');
}

function hideError(errorBox) {
    if (!errorBox) {
        return;
    }

    errorBox.textContent = '';
    errorBox.classList.add('d-none');
}