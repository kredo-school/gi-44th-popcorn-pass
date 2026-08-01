document.addEventListener('DOMContentLoaded', () => {

    const checkboxes = document.querySelectorAll('.genre-checkbox');

    checkboxes.forEach(checkbox => {

        checkbox.addEventListener('change', function () {

            const checked =
                document.querySelectorAll('.genre-checkbox:checked');

            if (checked.length > 3) {
                this.checked = false;
                alert('You can select up to 3 genres.');
            }

        });

    });

});