document.addEventListener('DOMContentLoaded', () => {
    // --------------------
    // Payment Method
    // --------------------
    const buttons =
        document.querySelectorAll('.payment-btn');

    const nextButton =
        document.getElementById('next-btn');

    const paymentMethodInput =
        document.getElementById('payment_method');

    if (
        buttons.length > 0
        && nextButton
        && paymentMethodInput
    ) {
        const forms = {
            paypal:
                document.getElementById('paypal-form'),

            onsite:
                document.getElementById('onsite-form'),
        };

        let selectedMethod =
            paymentMethodInput.value || 'paypal';

        function updatePaymentDisplay() {
            buttons.forEach(button => {
                button.classList.toggle(
                    'active',
                    button.dataset.method === selectedMethod
                );
            });

            Object.entries(forms).forEach(
                ([method, form]) => {
                    if (!form) {
                        return;
                    }

                    form.classList.toggle(
                        'd-none',
                        method !== selectedMethod
                    );
                }
            );

            paymentMethodInput.value = selectedMethod;
            nextButton.disabled = !selectedMethod;
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                selectedMethod = button.dataset.method;
                updatePaymentDisplay();
            });
        });

        updatePaymentDisplay();
    }

    // --------------------
    // Coupon
    // --------------------
    const couponRadios =
        document.querySelectorAll('.coupon-radio');

    const couponDiscountRow =
        document.getElementById('coupon-discount-row');

    const couponDiscount =
        document.getElementById('coupon-discount');

    const finalTotal =
        document.getElementById('final-total');

    if (
        couponRadios.length > 0
        && couponDiscountRow
        && couponDiscount
        && finalTotal
    ) {
        const baseTotal =
            parseFloat(finalTotal.dataset.baseTotal) || 0;

        couponRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                let discount = 0;

                if (
                    radio.dataset.type === 'percentage'
                ) {
                    discount =
                        baseTotal
                        * (
                            (parseFloat(
                                radio.dataset.percent
                            ) || 0)
                            / 100
                        );
                } else if (
                    radio.dataset.type === 'fixed_amount'
                ) {
                    discount =
                        parseFloat(
                            radio.dataset.amount
                        ) || 0;
                }

                discount = Math.min(
                    discount,
                    baseTotal
                );

                const newTotal = Math.max(
                    baseTotal - discount,
                    0
                );

                if (discount > 0) {
                    couponDiscountRow
                        .classList
                        .remove('d-none');

                    couponDiscount.textContent =
                        `-$${discount.toFixed(2)}`;
                } else {
                    couponDiscountRow
                        .classList
                        .add('d-none');

                    couponDiscount.textContent =
                        '-$0.00';
                }

                finalTotal.textContent =
                    `$${newTotal.toFixed(2)}`;
            });
        });
    }
});