document.addEventListener('DOMContentLoaded', () => {
    const editButton = document.querySelector('#edit-information-btn');
    const deleteForm = document.querySelector('#delete-information-form');
    const deleteButton = document.querySelector('#delete-information-btn');

    // Delete button
    deleteButton?.addEventListener('click', () => {
        if (deleteButton.classList.contains('disabled')) return;

        if (confirm('Are you sure you want to delete this information?')) {
            deleteForm.submit();
        }
    });

    // Information rows
    document.querySelectorAll('.information-row').forEach(row => {
        row.addEventListener('click', e => {
            if (e.target.type === 'checkbox') return;

            const informationId = row.dataset.informationId;

            editButton.href = `/admin/information/${informationId}/edit`;
            editButton.classList.remove('disabled');

            deleteForm.action = `/admin/information/${informationId}`;
            deleteButton.classList.remove('disabled');

            fetch(`/admin/information/${informationId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#detail-title').textContent = data.title ?? '—';
                    document.querySelector('#detail-category').textContent = data.category ?? '—';
                    document.querySelector('#detail-status').textContent = data.status ?? '—';
                    document.querySelector('#detail-content').textContent = data.content ?? '—';
                    document.querySelector('#detail-published-at').textContent =
                        data.published_at
                            ? data.published_at.substring(0, 10)
                            : '—';
                            
                    const imageContainer = document.querySelector('#detail-image');

                    if (data.image) {
                        imageContainer.innerHTML = `
                            <img src="/${data.image}"
                                alt="${data.title}"
                                class="img-fluid rounded"
                                style="max-width: 220px;">
                        `;
                    } else {
                        imageContainer.innerHTML = '—';
                    }
                });
        });
    });
});