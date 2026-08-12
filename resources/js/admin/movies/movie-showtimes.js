document.addEventListener('DOMContentLoaded', () => {

    const generateBtn =
        document.querySelector('#generate-btn');

    const cinemaSelect =
        document.querySelector('#gen-cinema');

    const screenSelect =
        document.querySelector('#gen-screen');

    const messageElement =
        document.querySelector('#generate-msg');

    const startDateInput =
        document.querySelector('#gen-start-date');

    const endDateInput =
        document.querySelector('#gen-end-date');



    if (
        !generateBtn ||
        !cinemaSelect ||
        !screenSelect
    ) {
        return;
    }


    // ===============================
    // Message
    // ===============================

    function showMessage(message, type = 'secondary') {

        if (!messageElement) {
            return;
        }

        messageElement.textContent = message;

        messageElement.className =
            `ms-3 text-${type} small`;
    }


    // ===============================
    // Generate Showtimes
    // ===============================

    generateBtn.addEventListener('click', async () => {

        const generateUrl =
            generateBtn.dataset.url;

        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

        const cinemaId =
            cinemaSelect.value;

        const screenId =
            screenSelect.value;

        const startDate =
            startDateInput?.value;

        const endDate =
            endDateInput?.value;


        const days = [
            ...document.querySelectorAll(
                '.gen-day:checked'
            )
        ].map((element) =>
            Number.parseInt(
                element.value,
                10
            )
        );


        const timeSlots = [
            ...document.querySelectorAll(
                '.gen-slot'
            )
        ]
            .map((element) =>
                element.value
            )
            .filter(Boolean);


        // ===============================
        // Validation
        // ===============================

        if (!cinemaId) {

            showMessage(
                'Please select a cinema.',
                'danger'
            );

            return;
        }


        if (!screenId) {

            showMessage(
                'Please select a screen.',
                'danger'
            );

            return;
        }


        if (!startDate || !endDate) {

            showMessage(
                'Please select start and end dates.',
                'danger'
            );

            return;
        }


        if (startDate > endDate) {

            showMessage(
                'End date must be after the start date.',
                'danger'
            );

            return;
        }


        if (days.length === 0) {

            showMessage(
                'Please select at least one day.',
                'danger'
            );

            return;
        }


        if (timeSlots.length === 0) {

            showMessage(
                'Please enter at least one time slot.',
                'danger'
            );

            return;
        }


        if (!generateUrl) {

            showMessage(
                'Generate URL is missing.',
                'danger'
            );

            return;
        }


        if (!csrfToken) {

            showMessage(
                'CSRF token is missing.',
                'danger'
            );

            return;
        }


        // ===============================
        // Loading
        // ===============================

        const originalText =
            generateBtn.innerHTML;

        generateBtn.disabled = true;

        generateBtn.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true"
            ></span>
            Generating...
        `;

        showMessage(
            'Generating showtimes...',
            'secondary'
        );


        // ===============================
        // FormData
        // ===============================

        const body = new FormData();

        body.append(
            '_token',
            csrfToken
        );

        body.append(
            'screen_id',
            screenId
        );

        body.append(
            'start_date',
            startDate
        );

        body.append(
            'end_date',
            endDate
        );


        days.forEach((day) => {

            body.append(
                'days[]',
                day
            );

        });


        timeSlots.forEach((slot) => {

            body.append(
                'time_slots[]',
                slot
            );

        });


        // ===============================
        // Request
        // ===============================

        try {

            const response =
                await fetch(
                    generateUrl,
                    {
                        method: 'POST',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        body,
                    }
                );


            const contentType =
                response.headers.get(
                    'content-type'
                );


            if (
                !contentType ||
                !contentType.includes(
                    'application/json'
                )
            ) {

                const html =
                    await response.text();

                console.error(
                    'Laravel HTML response:',
                    html
                );

                throw new Error(
                    `Laravel returned HTML instead of JSON. Status: ${response.status}`
                );
            }


            const data =
                await response.json();


            if (!response.ok) {

                const validationMessage =
                    data.errors
                        ? Object
                            .values(data.errors)
                            .flat()
                            .join(' ')
                        : null;


                throw new Error(
                    validationMessage ??
                    data.message ??
                    'Failed to generate showtimes.'
                );
            }


            // ===============================
            // Success
            // ===============================

            showMessage(
                data.message ??
                'Showtimes generated successfully.',
                'success'
            );


            console.log(
                'Showtime generation result:',
                data
            );


            setTimeout(() => {

                window.location.reload();

            }, 1500);


        } catch (error) {

            console.error(
                'Showtime generation error:',
                error
            );


            showMessage(
                error.message ??
                'Failed to generate showtimes.',
                'danger'
            );


            generateBtn.disabled = false;

            generateBtn.innerHTML =
                originalText;
        }
    });
});