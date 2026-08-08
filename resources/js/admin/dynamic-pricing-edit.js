const editor = document.getElementById('dynamicPricingEditor');

if (editor) {
    const basePriceInput = document.getElementById('base_price');
    const elasticityInput = document.getElementById(
        'elasticity_factor'
    );

    const elasticityValue = document.getElementById(
        'elasticity_value'
    );

    const previewBasePrice = document.getElementById(
        'preview_base_price'
    );

    const previewBasePriceCalc = document.getElementById(
        'preview_base_price_calc'
    );

    const previewElasticity = document.getElementById(
        'preview_elasticity'
    );

    const previewOccupancy = document.getElementById(
        'preview_occupancy'
    );

    const previewCurrentPrice = document.getElementById(
        'preview_current_price'
    );

    const previewMinPrice = document.getElementById(
        'preview_min_price'
    );

    const previewMaxPrice = document.getElementById(
        'preview_max_price'
    );

    const previewChangePercent = document.getElementById(
        'preview_change_percent'
    );

    const presetButtons = document.querySelectorAll(
        '.elasticity-preset'
    );

    const occupancy = Number(editor.dataset.occupancy) || 0;

    function updateElasticityValue() {
        const elasticity = Number(elasticityInput.value) || 0;

        elasticityValue.textContent = elasticity.toFixed(2);
    }

    function updatePreview() {
        const basePrice = Number(basePriceInput.value) || 0;
        const elasticity = Number(elasticityInput.value) || 0;

        const multiplier = 1 + occupancy * elasticity;

        const minPrice = basePrice * 0.85;
        const maxPrice = basePrice * 1.5;

        const calculatedPrice = basePrice * multiplier;

        const currentPrice = Math.max(
            minPrice,
            Math.min(maxPrice, calculatedPrice)
        );

        const changePercent =
            basePrice > 0
                ? ((currentPrice - basePrice) / basePrice) * 100
                : 0;

        previewBasePrice.textContent = Math.round(basePrice);
        previewBasePriceCalc.textContent = Math.round(basePrice);

        previewElasticity.textContent = elasticity.toFixed(2);
        previewOccupancy.textContent = occupancy.toFixed(2);

        previewCurrentPrice.textContent =
            Math.round(currentPrice);

        previewMinPrice.textContent = Math.round(minPrice);
        previewMaxPrice.textContent = Math.round(maxPrice);

        previewChangePercent.textContent =
            `${changePercent >= 0 ? '+' : ''}` +
            `${changePercent.toFixed(1)}%`;

        if (changePercent < -5) {
            previewChangePercent.className =
                'fw-bold text-success';
        } else if (changePercent > 5) {
            previewChangePercent.className =
                'fw-bold text-danger';
        } else {
            previewChangePercent.className =
                'fw-bold text-secondary';
        }
    }

    basePriceInput.addEventListener('input', updatePreview);

    elasticityInput.addEventListener('input', () => {
        updateElasticityValue();
        updatePreview();
    });

    presetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            elasticityInput.value = button.dataset.value;

            updateElasticityValue();
            updatePreview();
        });
    });

    updateElasticityValue();
    updatePreview();
}