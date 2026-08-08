document.addEventListener('DOMContentLoaded', () => {

    console.log('JS loaded');


    const btn = document.querySelector('#generate-btn');

    console.log('button:', btn);


    const generateUrl = document.querySelector('#generate-url')?.value;
    const movieId = document.querySelector('#movie-id')?.value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    


    btn?.addEventListener('click', () => {

        console.log('Generate clicked');


        fetch(generateUrl, {

            method: 'POST',

            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({

                movie_id: movieId,

                screen_id: document.querySelector('#gen-screen').value,

                start_date: document.querySelector('#gen-start-date').value,

                end_date: document.querySelector('#gen-end-date').value,

                days: [...document.querySelectorAll('.gen-day:checked')]
                    .map(el => el.value),

                slots: [...document.querySelectorAll('.gen-slot')]
                    .map(el => el.value)
                    .filter(v => v)

            })

        })
        .then(response => {

            console.log('status:', response.status);

            return response.json();

        })
        .then(data => {

            console.log('response:', data);

        })
        .catch(error => {

            console.error('error:', error);

        });


    });

});