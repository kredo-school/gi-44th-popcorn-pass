
document.addEventListener('DOMContentLoaded', () => {

    const chatArea = document.getElementById('chat-area');

    if (!chatArea) return;

    const fetchUrl = chatArea.dataset.fetchUrl;

    if (!fetchUrl) return;


    // =========================
    // Scroll to bottom
    // =========================

    function scrollBottom(behavior = 'smooth') {

        chatArea.scrollTo({
            top: chatArea.scrollHeight,
            behavior,
        });

    }


    // =========================
    // Remove HTML tags
    // =========================

    function stripHtml(html) {

        const temp = document.createElement('div');

        temp.innerHTML = html;

        return temp.textContent || temp.innerText || '';

    }


    // =========================
    // Create message
    // =========================

    function createMessageElement(message) {

        const senderNames = {

            customer: '👤 Customer',

            ai: '🤖 AI',

            staff: '👨‍💻 Staff',

        };


        const wrapper =
            document.createElement('div');

        const sender =
            document.createElement('strong');

        const text =
            document.createElement('p');


        wrapper.className =
            'admin-chat-message mb-3';


        sender.textContent =
            senderNames[message.sender_type] ?? 'Unknown';


        // =========================
        // Message text
        // =========================

        text.textContent =
            stripHtml(message.message ?? '');


        wrapper.append(
            sender,
            text
        );


        return wrapper;

    }


    // =========================
    // Load messages
    // =========================

    async function loadMessages() {

        try {

            const response =
                await fetch(fetchUrl, {

                    headers: {
                        Accept: 'application/json',
                    },

                });


            if (!response.ok) {

                throw new Error(
                    'Failed to load messages.'
                );

            }


            const data =
                await response.json();


            const fragment =
                document.createDocumentFragment();


            (data.messages ?? []).forEach(message => {

                fragment.appendChild(
                    createMessageElement(message)
                );

            });


            chatArea.replaceChildren(
                fragment
            );


            scrollBottom();


        } catch (error) {

            console.error(
                'Failed to load chat messages:',
                error
            );

        }

    }


    // =========================
    // Initial load
    // =========================

    scrollBottom('auto');

    loadMessages();


    // =========================
    // Reload every 5 seconds
    // =========================

    setInterval(
        loadMessages,
        5000
    );

});