document.addEventListener('DOMContentLoaded', () => {
    const userRows = document.querySelectorAll('.user-row');
    const updateForm = document.querySelector('#user-update-form');
    const saveButton = document.querySelector('#user-save-btn');

    // Only run on the Users index page.
    if (!userRows.length || !updateForm || !saveButton) return;

    const detailUsername = document.querySelector('#detail-username');
    const detailEmail = document.querySelector('#detail-email');
    const detailPhone = document.querySelector('#detail-phone');
    const detailDob = document.querySelector('#detail-dob');
    const detailLastLogin = document.querySelector('#detail-last-login');
    const detailCreated = document.querySelector('#detail-created');
    const detailRole = document.querySelector('#detail-role');
    const detailIsActive = document.querySelector('#detail-is-active');

    userRows.forEach((row) => {
        row.addEventListener('click', async () => {
            const userId = row.dataset.userId;
            if (!userId) return;

            try {
                const response = await fetch(`/admin/users/${userId}/details`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(`Failed to load user details: ${response.status}`);
                }

                const data = await response.json();

                if (detailUsername) detailUsername.textContent = data.username || '—';
                if (detailEmail) detailEmail.textContent = data.email || '—';
                if (detailPhone) detailPhone.textContent = data.phone || '—';
                if (detailDob) detailDob.textContent = data.date_of_birth || '—';
                if (detailLastLogin) detailLastLogin.textContent = data.last_login_at || '—';
                if (detailCreated) detailCreated.textContent = data.created_at || '—';
                if (detailRole) detailRole.value = data.role;

                if (detailIsActive) {
                    detailIsActive.checked =
                        data.is_active === true || Number(data.is_active) === 1;
                }

                updateForm.action = `/admin/users/${data.id}`;
                saveButton.disabled = false;
            } catch (error) {
                console.error(error);
            }
        });
    });
});