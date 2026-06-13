{{-- Floating AI Button --}}
<button class="ai-floating-btn" id="aiFloatingBtn" onclick="toggleAiChat()" aria-label="Open AI Assistant">
    <div class="ai-floating-pulse"></div>
    <div class="ai-floating-icon">
        <i class="fas fa-wand-magic-sparkles"></i>
    </div>
</button>

{{-- AI Chat Panel --}}
<aside class="ai-chat-panel" id="aiChatPanel" aria-hidden="true">

    {{-- Header --}}
    <div class="ai-chat-header">
        <div class="ai-chat-header-left">
            <div class="ai-chat-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-chat-header-info">
                <h3 class="ai-chat-header-title">AI Assistant</h3>
                <span class="ai-chat-header-subtitle">Powered by Groq</span>
            </div>
        </div>
        <div class="ai-chat-header-actions">
            <button class="ai-chat-header-btn" onclick="clearChatHistory()" title="Clear conversation" aria-label="Clear chat">
                <i class="fas fa-broom"></i>
            </button>
            <button class="ai-chat-header-btn ai-chat-header-close" onclick="toggleAiChat()" title="Close assistant" aria-label="Close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>

    {{-- Messages --}}
    <div class="ai-chat-body" id="aiChatBody">
        <div class="ai-message ai-message-bot">
            <div class="ai-message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-message-bubble">
                <p>Hello! I am your AI assistant. I can help you with books, orders, customers, revenue, and more. Try asking me something!</p>
            </div>
        </div>
    </div>

    {{-- Suggestions --}}
    <div class="ai-suggestions-wrapper" id="aiSuggestionsWrapper">
        <span class="ai-suggestions-label">Suggested Questions</span>
        <div class="ai-suggestions" id="aiSuggestions">
            <button class="ai-suggestion-chip" onclick="sendPrompt('How many books do we have?')">
                <i class="fas fa-book"></i> Total books?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('What is the total revenue?')">
                <i class="fas fa-coins"></i> Revenue?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('Show low stock alerts')">
                <i class="fas fa-triangle-exclamation"></i> Low stock?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('What are the best selling books?')">
                <i class="fas fa-trophy"></i> Best sellers?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('How many customers do we have?')">
                <i class="fas fa-users"></i> Customers?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('How many orders so far?')">
                <i class="fas fa-receipt"></i> Orders?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('What categories do we have?')">
                <i class="fas fa-layer-group"></i> Categories?
            </button>
            <button class="ai-suggestion-chip" onclick="sendPrompt('How many new customers this week?')">
                <i class="fas fa-user-plus"></i> New customers?
            </button>
        </div>
    </div>

    {{-- Input --}}
    <div class="ai-chat-footer">
        <div class="ai-chat-input-wrapper">
            <input
                type="text"
                id="aiChatInput"
                class="ai-chat-input"
                placeholder="Ask anything about your store..."
                onkeypress="if(event.key==='Enter')sendMessage()"
                autocomplete="off"
            >
            <button class="ai-chat-send-btn" onclick="sendMessage()" aria-label="Send message">
                <i class="fas fa-arrow-up"></i>
            </button>
        </div>
        <p class="ai-chat-disclaimer">Responses are AI-generated. Verify critical data.</p>
    </div>

</aside>

{{-- Overlay for mobile --}}
<div class="ai-chat-overlay" id="aiChatOverlay" onclick="toggleAiChat()"></div>