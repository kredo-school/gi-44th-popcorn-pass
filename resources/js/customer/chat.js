document.addEventListener('DOMContentLoaded', function () {


    const chatArea = document.getElementById('chat-area');

    const fetchUrl = chatArea.dataset.fetchUrl;



    function loadMessages(){


        fetch(fetchUrl)

        .then(response => response.json())

        .then(data => {


            chatArea.innerHTML = '';



            data.messages.forEach(message => {


                let html = '';



                if(message.sender_type === 'ai'){


                    html = `
                    
                    <div class="message mb-4">

                        <div class="fw-bold">
                            🤖 AI
                        </div>

                        <div class="bubble ai">
                            ${message.message}
                        </div>

                    </div>

                    `;


                }


                else if(message.sender_type === 'customer'){


                    html = `

                    <div class="message mb-4 text-end">

                        <div class="fw-bold">
                            👤 You
                        </div>

                        <div class="bubble customer">
                            ${message.message}
                        </div>

                    </div>

                    `;


                }


                else if(message.sender_type === 'staff'){


                    html = `

                    <div class="message mb-4">

                        <div class="fw-bold">
                            👨‍💻 Staff
                        </div>

                        <div class="bubble staff">
                            ${message.message}
                        </div>

                    </div>

                    `;


                }



                chatArea.insertAdjacentHTML(
                    'beforeend',
                    html
                );


            });

            chatArea.scrollTop =
                chatArea.scrollHeight;

        });

    }

    // Initial load

    loadMessages();



    // renew every five seconds

    setInterval(
        loadMessages,
        5000
    );


});