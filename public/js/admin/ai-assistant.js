(function () {
    "use strict";

    const STORAGE_KEY = "bookshop_ai_chat_history";
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfToken ? csrfToken.content : "";

    let isOpen = false;
    let isTyping = false;
    let chatHistory = [];

    // Initialize System Lifecycle
    document.addEventListener("DOMContentLoaded", function () {
        loadChatHistory();
    });

    // ========== STATE MANAGEMENT & LOCAL STORAGE ==========
    function loadChatHistory() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            try {
                chatHistory = JSON.parse(stored);
            } catch (e) {
                chatHistory = [];
                localStorage.removeItem(STORAGE_KEY);
            }
        }
        renderHistoryDOM();
    }

    function saveChatHistory() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));
        updateUiLayoutState();
    }

    function updateUiLayoutState() {
        const wrapper = document.getElementById("aiSuggestionsWrapper");
        if (!wrapper) return;

        // Mutate UI layout state based on message count parameters
        if (chatHistory.length > 0) {
            wrapper.classList.add("has-history");
        } else {
            wrapper.classList.remove("has-history");
        }
    }

    // ========== DOM RENDER PIPELINE ==========
    function renderHistoryDOM() {
        const body = document.getElementById("aiChatBody");
        if (!body) return;

        // Base Welcome Layout
        body.innerHTML = `
            <div class="ai-message ai-message-bot">
                <div class="ai-message-avatar"><i class="fas fa-robot"></i></div>
                <div class="ai-message-bubble">
                    <p>Hello! I am your AI assistant. I can help you with books, orders, customers, revenue, and more. Try asking me something!</p>
                </div>
            </div>
        `;

        chatHistory.forEach(function (msg) {
            appendMessageDOM(msg.content, msg.role, false);
        });

        updateUiLayoutState();
        scrollToBottom();
    }

    // ========== TOGGLE PANEL INTERACTION ==========
    window.toggleAiChat = function () {
        const panel = document.getElementById("aiChatPanel");
        const overlay = document.getElementById("aiChatOverlay");
        const btn = document.getElementById("aiFloatingBtn");
        if (!panel) return;

        isOpen = !isOpen;

        if (isOpen) {
            panel.classList.add("open");
            panel.setAttribute("aria-hidden", "false");
            if (overlay) overlay.classList.add("show");
            document.body.style.overflow = "hidden";
            document.getElementById("aiChatInput")?.focus();
            scrollToBottom();
        } else {
            panel.classList.remove("open");
            panel.setAttribute("aria-hidden", "true");
            if (overlay) overlay.classList.remove("show");
            document.body.style.overflow = "";
        }
    };

    // ========== CONVERSATION DISPATCH ENGINE ==========
    window.sendMessage = function () {
        const input = document.getElementById("aiChatInput");
        if (!input || isTyping) return;

        const message = input.value.trim();
        if (!message) return;

        input.value = "";
        executeSequence(message);
    };

    window.sendPrompt = function (prompt) {
        if (isTyping) return;
        executeSequence(prompt);
    };

    function executeSequence(text) {
        // 1. Commit and persist User Input
        chatHistory.push({ role: "user", content: text });
        appendMessageDOM(text, "user", true);
        saveChatHistory();

        // 2. State shift UI and invoke worker animation
        showTypingIndicator();
        scrollToBottom();

        // 3. Dispatch to internal proxy routes
        fetchAIResponse(text);
    }

    // ========== NETWORK CLIENT ==========
    function fetchAIResponse(message) {
        isTyping = true;

        fetch("/admin/ai/ask", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
                Accept: "application/json",
            },
            body: JSON.stringify({ message: message }),
        })
            .then(function (res) {
                if (!res.ok) throw new Error("Server communication fault.");
                return res.json();
            })
            .then(function (data) {
                removeTypingIndicator();
                const reply = data.response || "No data payload returned from interface handler.";
                
                chatHistory.push({ role: "bot", content: reply });
                appendMessageDOM(reply, "bot", true);
                saveChatHistory();
                
                isTyping = false;
                scrollToBottom();
            })
            .catch(function (err) {
                removeTypingIndicator();
                appendMessageDOM("System fault encountered: Unable to resolve data engine.", "bot", false);
                isTyping = false;
                scrollToBottom();
            });
    }

    // ========== ATOMIC DOM MANIPULATION HELPMATES ==========
    function appendMessageDOM(content, role, animate) {
        const body = document.getElementById("aiChatBody");
        if (!body) return;

        const wrapper = document.createElement("div");
        wrapper.className = "ai-message ai-message-" + role;
        if (!animate) wrapper.style.animation = "none";

        const avatar = document.createElement("div");
        avatar.className = "ai-message-avatar";
        avatar.innerHTML = role === "user" ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';

        const bubble = document.createElement("div");
        bubble.className = "ai-message-bubble";
        bubble.innerHTML = "<p>" + formatMarkdownParser(content) + "</p>";

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        body.appendChild(wrapper);
    }

    function showTypingIndicator() {
        const body = document.getElementById("aiChatBody");
        if (!body || document.getElementById("aiTypingIndicator")) return;

        const typing = document.createElement("div");
        typing.className = "ai-message ai-message-bot ai-message-typing";
        typing.id = "aiTypingIndicator";

        typing.innerHTML = `
            <div class="ai-message-avatar"><i class="fas fa-robot"></i></div>
            <div class="ai-message-bubble">
                <span class="ai-typing-dot"></span><span class="ai-typing-dot"></span><span class="ai-typing-dot"></span>
            </div>
        `;
        body.appendChild(typing);
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById("aiTypingIndicator");
        if (indicator) indicator.remove();
    }

    window.clearChatHistory = function () {
        chatHistory = [];
        localStorage.removeItem(STORAGE_KEY);
        renderHistoryDOM();
    };

    function scrollToBottom() {
        const body = document.getElementById("aiChatBody");
        if (body) {
            body.scrollTop = body.scrollHeight;
        }
    }

    function formatMarkdownParser(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
            .replace(/\n/g, "<br>");
    }

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && isOpen) toggleAiChat();
    });
})();