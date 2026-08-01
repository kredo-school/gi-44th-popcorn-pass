document.addEventListener('DOMContentLoaded', function () {
    const generateBtn = document.querySelector('#generate-btn');

    const cinemaSelect = document.querySelector('#gen-cinema');
    const screenSelect = document.querySelector('#gen-screen');

    cinemaSelect?.addEventListener('change', function () {
        const cinemaId = this.value;

        screenSelect.value = '';

        Array.from(screenSelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                !cinemaId || option.dataset.cinema !== cinemaId;
        });

        screenSelect.disabled = !cinemaId;
    });

    if (!generateBtn) {
        return;
    }

    const generateUrl = generateBtn.dataset.url;

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');


    generateBtn.addEventListener('click', function () {
        const cinemaId =
            document.querySelector('#gen-cinema')?.value;

        const screenId =
            document.querySelector('#gen-screen')?.value;

        const days = [
            ...document.querySelectorAll('.gen-day:checked'),
        ].map((element) => Number.parseInt(element.value, 10));

        const timeSlots = [
            ...document.querySelectorAll('.gen-slot'),
        ]
            .map((element) => element.value)
            .filter(Boolean);

        const msgEl = document.querySelector('#generate-msg');

        if (!cinemaId || !screenId || days.length === 0) {
            msgEl.textContent =
                'Please select a cinema, a screen, and at least one day.';

            msgEl.className =
                'ms-3 text-danger small';

            return;
        }

        if (timeSlots.length === 0) {
            msgEl.textContent =
                'Please enter at least one time slot.';
            msgEl.className = 'ms-3 text-danger small';
            return;
        }

        if (!generateUrl || !csrfToken) {
            msgEl.textContent =
                'The generate URL or CSRF token is missing.';
            msgEl.className = 'ms-3 text-danger small';
            return;
        }

        msgEl.textContent = 'Generating...';
        msgEl.className = 'ms-3 text-secondary small';

        const body = new FormData();

        body.append('_token', csrfToken);
        body.append('screen_id', screenId);

        days.forEach((day) => {
            body.append('days[]', day);
        });

        timeSlots.forEach((slot) => {
            body.append('time_slots[]', slot);
        });

        fetch(generateUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
            },
            body,
        })
            .then(async (response) => {
                const contentType =
                    response.headers.get('content-type');

                const text = await response.text();

                console.log('Status:', response.status);
                console.log('Response:', text);

                if (
                    !contentType ||
                    !contentType.includes('application/json')
                ) {
                    throw new Error(
                        `Laravel returned HTML instead of JSON. Status: ${response.status}`
                    );
                }

                const data = JSON.parse(text);

                if (!response.ok) {
                    const validationMessage = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : null;

                    throw new Error(
                        validationMessage ??
                        data.message ??
                        'Failed to generate showtimes.'
                    );
                }

                return data;
            })
            .then((data) => {
                msgEl.textContent = data.message;
                msgEl.className = 'ms-3 text-success small';

                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            })
            .catch((error) => {
                console.error(error);

                msgEl.textContent = error.message;
                msgEl.className = 'ms-3 text-danger small';
            });
    });
});