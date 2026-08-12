document.addEventListener('DOMContentLoaded', () => {
    const avatarInput = document.getElementById('avatar');

    // Only run on the Profile Edit page.
    if (!avatarInput) return;

    avatarInput.addEventListener('change', () => {
        const file = avatarInput.files?.[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();

        reader.addEventListener('load', (event) => {
            const preview = document.getElementById('avatarPreview');
            if (!preview) return;

            if (preview.tagName === 'DIV') {
                const image = document.createElement('img');
                image.src = event.target.result;
                image.id = 'avatarPreview';
                image.className = 'mypage-profile-avatar rounded-circle';
                image.alt = 'Avatar Preview';
                preview.replaceWith(image);
            } else {
                preview.src = event.target.result;
            }
        });

        reader.readAsDataURL(file);
    });
});