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
    // Promotion and Coupon
    // --------------------
    const promotionCheckbox = document.getElementById('use_promotion');
    const promotionDiscountRow = document.getElementById('promotion-discount-row');
    const promotionDiscountElement = document.getElementById('promotion-discount');
    const couponRadios = document.querySelectorAll('.coupon-radio');
    const couponDiscountRow = document.getElementById('coupon-discount-row');
    const couponDiscount = document.getElementById('coupon-discount');
    const finalTotal = document.getElementById('final-total');

    if (finalTotal) {
        const subtotal =
            parseFloat(finalTotal.dataset.subtotal) || 0;

        const availablePromotionDiscount =
            parseFloat(
                finalTotal.dataset.promotionDiscount
            ) || 0;

        function updateDiscounts() {
            const usePromotion =
                promotionCheckbox?.checked ?? false;

            const appliedPromotionDiscount =
                usePromotion
                    ? Math.min(
                        availablePromotionDiscount,
                        subtotal
                    )
                    : 0;

            const amountAfterPromotion = Math.max(
                subtotal - appliedPromotionDiscount,
                0
            );

            if (promotionDiscountRow) {
                promotionDiscountRow.classList.toggle(
                    'd-none',
                    !usePromotion
                );
            }

            if (promotionDiscountElement) {
                promotionDiscountElement.textContent =
                    `-$${appliedPromotionDiscount.toFixed(2)}`;
            }

            const selectedCoupon =
                document.querySelector(
                    '.coupon-radio:checked'
                );

            let appliedCouponDiscount = 0;

            if (selectedCoupon) {
                if (
                    selectedCoupon.dataset.type ===
                    'percentage'
                ) {
                    appliedCouponDiscount =
                        amountAfterPromotion
                        * (
                            (
                                parseFloat(
                                    selectedCoupon.dataset.percent
                                ) || 0
                            )
                            / 100
                        );
                } else if (
                    selectedCoupon.dataset.type ===
                    'fixed_amount'
                ) {
                    appliedCouponDiscount =
                        parseFloat(
                            selectedCoupon.dataset.amount
                        ) || 0;
                }
            }

            appliedCouponDiscount = Math.min(
                appliedCouponDiscount,
                amountAfterPromotion
            );

            const newTotal = Math.max(
                amountAfterPromotion
                    - appliedCouponDiscount,
                0
            );

            if (
                couponDiscountRow &&
                couponDiscount
            ) {
                couponDiscountRow.classList.toggle(
                    'd-none',
                    appliedCouponDiscount <= 0
                );

                couponDiscount.textContent =
                    `-$${appliedCouponDiscount.toFixed(2)}`;
            }

            finalTotal.textContent =
                `$${newTotal.toFixed(2)}`;
        }

        promotionCheckbox?.addEventListener(
            'change',
            updateDiscounts
        );

        couponRadios.forEach(radio => {
            radio.addEventListener(
                'change',
                updateDiscounts
            );
        });

        updateDiscounts();
    }
});