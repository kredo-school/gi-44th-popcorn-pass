document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('cinemaReviewModal');
    const form = document.getElementById('cinemaReviewForm');

    if (!modal || !form) return;

    const cinemaIdInput = document.getElementById('cinemaId');
    const cinemaNameDisplay = document.getElementById('cinemaNameDisplay');
    const visitedDatePicker = document.getElementById('visitedDatePicker');
    const errorAlert = document.getElementById('reviewErrorAlert');
    const submitButton = document.getElementById('submitReviewBtn');

    modal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;

        form.reset();

        cinemaIdInput.value = button.dataset.cinemaId || '';
        cinemaNameDisplay.textContent = button.dataset.cinemaName || '';
        visitedDatePicker.value = button.dataset.visitedDate || '';

        document.querySelectorAll('.rating-input').forEach(input => {
            input.value = '';
        });

        document.querySelectorAll('.star-btn').forEach(button => {
            button.classList.remove('btn-warning');
            button.classList.add('btn-outline-warning');
        });

        document.querySelectorAll('.rating-value').forEach(element => {
            element.textContent = '-';
        });

        errorAlert.classList.add('d-none');
        errorAlert.classList.remove('alert-success');
        errorAlert.classList.add('alert-danger');
    });

    document.querySelectorAll('.rating-dimension').forEach(container => {
        const buttons = container.querySelectorAll('.star-btn');
        const input = container.querySelector('.rating-input');
        const valueDisplay = container.querySelector('.rating-value');

        buttons.forEach((button, index) => {
            button.addEventListener('click', event => {
                event.preventDefault();

                const value = Number(button.dataset.value);

                input.value = value;
                valueDisplay.textContent = value;

                buttons.forEach((starButton, starIndex) => {
                    const isSelected = starIndex < value;

                    starButton.classList.toggle(
                        'btn-warning',
                        isSelected
                    );

                    starButton.classList.toggle(
                        'btn-outline-warning',
                        !isSelected
                    );
                });
            });
        });
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const ratingInputs = [
            ...document.querySelectorAll('.rating-input'),
        ];

        if (!ratingInputs.every(input => input.value)) {
            showError('Please rate all dimensions.');
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

        try {
            const response = await fetch(form.dataset.storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    Accept: 'application/json',
                },
                body: new FormData(form),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.error
                    || data.message
                    || 'Review submission failed.'
                );
            }

            errorAlert.classList.remove(
                'd-none',
                'alert-danger'
            );

            errorAlert.classList.add('alert-success');
            errorAlert.textContent = '✓ Review submitted!';

            setTimeout(() => {
                bootstrap.Modal.getInstance(modal)?.hide();
                window.location.reload();
            }, 1500);
        } catch (error) {
            showError(error.message);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML =
                '<i class="fa-solid fa-paper-plane me-2"></i>Submit Review';
        }
    });

    function showError(message) {
        errorAlert.textContent = message;
        errorAlert.classList.remove(
            'd-none',
            'alert-success'
        );

        errorAlert.classList.add('alert-danger');
    }
});