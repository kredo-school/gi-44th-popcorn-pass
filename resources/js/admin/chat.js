console.log("admin chat loaded");


document.addEventListener(
    'DOMContentLoaded',
    function () {


        const chatArea =
            document.getElementById('chat-area');


        if (!chatArea) {
            return;
        }


        const fetchUrl =
            chatArea.dataset.fetchUrl;


        function scrollBottom() {

            chatArea.scrollTo({

                top: chatArea.scrollHeight,

                behavior: "smooth"

            });

        }




        function loadMessages() {


            fetch(fetchUrl)


                .then(response => response.json())


                .then(data => {


                    chatArea.innerHTML = "";



                    data.messages.forEach(message => {


                        let type = "";



                        if (message.sender_type === "customer") {


                            type = "👤 Customer";


                        } else if (message.sender_type === "ai") {


                            type = "🤖 AI";


                        } else if (message.sender_type === "staff") {


                            type = "👨‍💻 Staff";


                        }



                        chatArea.innerHTML += `

                            <div class="mb-3">

                                <strong>
                                    ${type}
                                </strong>

                                <p>
                                    ${message.message}
                                </p>

                            </div>

                        `;


                    });


                    setTimeout(() => {

                        scrollBottom();

                    }, 100);



                });


        }



        setTimeout(() => {

            scrollBottom();

        }, 100);



        loadMessages();


        setInterval(

            loadMessages,

            5000

        );


    }
);