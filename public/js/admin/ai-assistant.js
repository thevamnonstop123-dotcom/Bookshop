/* =============================================
   AI Assistant Chat - Premium
   ============================================= */

const CHAT_KEY = 'bookshop_ai_chat_history';

function toggleAiChat() {
    const modal = document.getElementById('aiChatModal');
    modal.classList.toggle('open');
    if (modal.classList.contains('open')) {
        document.getElementById('aiChatInput').focus();
        loadChatHistory();
    }
}

function sendMessage() {
    const input = document.getElementById('aiChatInput');
    const message = input.value.trim();
    if (!message) return;

    addMessage('user', message);
    input.value = '';
    saveChatHistory();

    const body = document.getElementById('aiChatBody');
    const loading = document.createElement('div');
    loading.className = 'ai-message ai-bot';
    loading.innerHTML = '<div class="ai-message-content"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
    body.appendChild(loading);
    body.scrollTop = body.scrollHeight;

    fetch('/admin/ai-assistant/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    })
    .then(r => r.json())
    .then(data => {
        loading.remove();
        addMessage('bot', data.reply);
        saveChatHistory();
    })
    .catch(() => {
        loading.remove();
        addMessage('bot', 'Sorry, something went wrong.');
    });
}

function sendPrompt(message) {
    addMessage('user', message);
    saveChatHistory();

    const body = document.getElementById('aiChatBody');
    const loading = document.createElement('div');
    loading.className = 'ai-message ai-bot';
    loading.innerHTML = '<div class="ai-message-content"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
    body.appendChild(loading);
    body.scrollTop = body.scrollHeight;

    fetch('/admin/ai-assistant/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    })
    .then(r => r.json())
    .then(data => {
        loading.remove();
        addMessage('bot', data.reply);
        saveChatHistory();
    })
    .catch(() => {
        loading.remove();
        addMessage('bot', 'Sorry, something went wrong.');
    });
}

function addMessage(type, text) {
    const body = document.getElementById('aiChatBody');
    const div = document.createElement('div');
    div.className = 'ai-message ai-' + type;
    div.innerHTML = '<div class="ai-message-content">' + text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>') + '</div>';
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;
}

function saveChatHistory() {
    const messages = [];
    document.querySelectorAll('#aiChatBody .ai-message').forEach(el => {
        const type = el.classList.contains('ai-user') ? 'user' : 'bot';
        const text = el.querySelector('.ai-message-content').innerHTML;
        messages.push({ type, text });
    });
    if (messages.length > 20) messages.splice(0, messages.length - 20);
    localStorage.setItem(CHAT_KEY, JSON.stringify(messages));
}

function loadChatHistory() {
    const data = localStorage.getItem(CHAT_KEY);
    if (!data) return;
    try {
        const messages = JSON.parse(data);
        const body = document.getElementById('aiChatBody');
        body.innerHTML = '';
        messages.forEach(msg => addMessage(msg.type, msg.text));
    } catch(e) {}
}

function clearChatHistory() {
    if (confirm('Clear all chat history?')) {
        localStorage.removeItem(CHAT_KEY);
        document.getElementById('aiChatBody').innerHTML = `
            <div class="ai-message ai-bot">
                <div class="ai-message-content">👋 Hello! I'm your AI assistant. I can tell you about books, orders, customers, and revenue. Try asking me something!</div>
            </div>`;
    }
}