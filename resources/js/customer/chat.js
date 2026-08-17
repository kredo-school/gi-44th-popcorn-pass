
document.addEventListener('DOMContentLoaded', function () {

    const chatArea = document.getElementById('chat-area');

    if (!chatArea) {
        return;
    }

    const fetchUrl = chatArea.dataset.fetchUrl;

    function loadMessages() {

        fetch(fetchUrl)
            .then(response => response.json())
            .then(data => {

                chatArea.innerHTML = '';

                data.messages.forEach(message => {

                    const messageWrapper = document.createElement('div');

                    messageWrapper.classList.add(
                        'message',
                        'mb-4'
                    );

                    // =========================
                    // AI
                    // =========================

                    if (message.sender_type === 'ai') {

                        messageWrapper.innerHTML = `
                            <div class="fw-bold">
                                🤖 AI
                            </div>

                            <div class="bubble ai"></div>
                        `;

                        const bubble =
                            messageWrapper.querySelector('.bubble');

                        // AI message can contain HTML
                        bubble.innerHTML = message.message;

                    }

                    // =========================
                    // Customer
                    // =========================

                    else if (message.sender_type === 'customer') {

                        messageWrapper.classList.add('text-end');

                        messageWrapper.innerHTML = `
                            <div class="fw-bold">
                                👤 You
                            </div>

                            <div class="bubble customer"></div>
                        `;

                        const bubble =
                            messageWrapper.querySelector('.bubble');

                        // Customer message must be treated as text
                        bubble.textContent = message.message;

                    }

                    // =========================
                    // Staff
                    // =========================

                    else if (message.sender_type === 'staff') {

                        messageWrapper.innerHTML = `
                            <div class="fw-bold">
                                👨‍💻 Staff
                            </div>

                            <div class="bubble staff"></div>
                        `;

                        const bubble =
                            messageWrapper.querySelector('.bubble');

                        // Staff message must be treated as text
                        bubble.textContent = message.message;
                    }

                    chatArea.appendChild(messageWrapper);
                });

                chatArea.scrollTop =
                    chatArea.scrollHeight;

            })
            .catch(error => {
                console.error(
                    'Failed to load chat messages:',
                    error
                );
            });
    }

    // Initial load
    loadMessages();

    // Reload every 5 seconds
    setInterval(
        loadMessages,
        5000
    );

});