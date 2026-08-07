document.addEventListener('DOMContentLoaded', () => {
    const chatArea = document.getElementById('chat-area');

    if (!chatArea) return;

    const fetchUrl = chatArea.dataset.fetchUrl;

    if (!fetchUrl) return;

    function scrollBottom(behavior = 'smooth') {
        chatArea.scrollTo({
            top: chatArea.scrollHeight,
            behavior,
        });
    }

    function createMessageElement(message) {
        const senderNames = {
            customer: '👤 Customer',
            ai: '🤖 AI',
            staff: '👨‍💻 Staff',
        };

        const wrapper = document.createElement('div');
        const sender = document.createElement('strong');
        const text = document.createElement('p');

        wrapper.className = 'admin-chat-message mb-3';
        sender.textContent = senderNames[message.sender_type] ?? 'Unknown';
        text.textContent = message.message ?? '';

        wrapper.append(sender, text);

        return wrapper;
    }

    async function loadMessages() {
        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load messages.');
            }

            const data = await response.json();
            const fragment = document.createDocumentFragment();

            (data.messages ?? []).forEach(message => {
                fragment.appendChild(createMessageElement(message));
            });

            chatArea.replaceChildren(fragment);
            scrollBottom();
        } catch (error) {
            console.error(error);
        }
    }

    scrollBottom('auto');
    loadMessages();

    setInterval(loadMessages, 5000);
});