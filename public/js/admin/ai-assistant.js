/**
 * Bookshop AI Assistant — Chat Interactions
 * Toggle, send/receive messages, suggestions, clear history
 */
(function () {
    "use strict";

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfToken ? csrfToken.content : "";

    let isOpen = false;
    let isTyping = false;

    // ========== TOGGLE ==========
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
            if (btn) btn.style.display = "none";
            document.body.style.overflow = "hidden";
            document.getElementById("aiChatInput")?.focus();
            scrollToBottom();
        } else {
            panel.classList.remove("open");
            panel.setAttribute("aria-hidden", "true");
            if (overlay) overlay.classList.remove("show");
            if (btn) btn.style.display = "";
            document.body.style.overflow = "";
        }
    };

    // ========== SEND MESSAGE ==========
    window.sendMessage = function () {
        const input = document.getElementById("aiChatInput");
        if (!input || isTyping) return;

        const message = input.value.trim();
        if (!message) return;

        appendMessage(message, "user");
        input.value = "";
        hideSuggestions();
        showTyping();
        scrollToBottom();

        fetchAIResponse(message);
    };

    // ========== SEND PROMPT ==========
    window.sendPrompt = function (prompt) {
        const input = document.getElementById("aiChatInput");
        if (!input || isTyping) return;

        appendMessage(prompt, "user");
        hideSuggestions();
        showTyping();
        scrollToBottom();

        fetchAIResponse(prompt);
    };

    // ========== CLEAR CHAT ==========
    window.clearChatHistory = function () {
        const body = document.getElementById("aiChatBody");
        const suggestions = document.getElementById("aiSuggestionsWrapper");

        if (!body) return;

        body.innerHTML = `
            <div class="ai-message ai-message-bot">
                <div class="ai-message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-message-bubble">
                    <p>Chat cleared. How can I help you?</p>
                </div>
            </div>
        `;

        if (suggestions) suggestions.style.display = "";
    };

    // ========== API CALL ==========
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
                return res.json();
            })
            .then(function (data) {
                removeTyping();
                if (data.response) {
                    appendMessage(data.response, "bot");
                } else {
                    appendMessage(
                        "Sorry, I could not process that request. Please try again.",
                        "bot",
                    );
                }
                isTyping = false;
                scrollToBottom();
            })
            .catch(function () {
                removeTyping();
                appendMessage(
                    "Something went wrong. Please check your connection and try again.",
                    "bot",
                );
                isTyping = false;
                scrollToBottom();
            });
    }

    // ========== APPEND MESSAGE ==========
    function appendMessage(content, role) {
        const body = document.getElementById("aiChatBody");
        if (!body) return;

        const wrapper = document.createElement("div");
        wrapper.className = "ai-message ai-message-" + role;

        const avatar = document.createElement("div");
        avatar.className = "ai-message-avatar";
        avatar.innerHTML =
            role === "user"
                ? '<i class="fas fa-user"></i>'
                : '<i class="fas fa-robot"></i>';

        const bubble = document.createElement("div");
        bubble.className = "ai-message-bubble";
        bubble.innerHTML = "<p>" + formatMessage(content) + "</p>";

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        body.appendChild(wrapper);
    }

    // ========== TYPING INDICATOR ==========
    function showTyping() {
        const body = document.getElementById("aiChatBody");
        if (!body) return;

        const typing = document.createElement("div");
        typing.className = "ai-message ai-message-bot ai-message-typing";
        typing.id = "aiTypingIndicator";

        const avatar = document.createElement("div");
        avatar.className = "ai-message-avatar";
        avatar.innerHTML = '<i class="fas fa-robot"></i>';

        const bubble = document.createElement("div");
        bubble.className = "ai-message-bubble";
        bubble.innerHTML =
            '<span class="ai-typing-dot"></span><span class="ai-typing-dot"></span><span class="ai-typing-dot"></span>';

        typing.appendChild(avatar);
        typing.appendChild(bubble);
        body.appendChild(typing);
    }

    function removeTyping() {
        const indicator = document.getElementById("aiTypingIndicator");
        if (indicator) indicator.remove();
    }

    // ========== HELPERS ==========
    function hideSuggestions() {
        const suggestions = document.getElementById("aiSuggestionsWrapper");
        if (suggestions) suggestions.style.display = "none";
    }

    function scrollToBottom() {
        const body = document.getElementById("aiChatBody");
        if (body) {
            setTimeout(function () {
                body.scrollTop = body.scrollHeight;
            }, 100);
        }
    }

    function formatMessage(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
            .replace(/\n/g, "<br>");
    }

    // ========== KEYBOARD ==========
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && isOpen) {
            toggleAiChat();
        }
    });
})();
