{{-- Floating AI Button --}}
<button class="ai-floating-btn" onclick="toggleAiChat()" title="AI Assistant">
    <div class="ai-orbital"></div>
    <div class="ai-orbital"></div>
    <div class="ai-orbital"></div>
    🤖
</button>

{{-- Chat Modal --}}
<div class="ai-chat-modal" id="aiChatModal">
    <div class="ai-chat-header">
        <div class="ai-chat-header-left">
            <div class="ai-avatar">🤖</div>
            <div>
                <strong>AI Assistant</strong>
                <small>Powered by Groq</small>
            </div>
        </div>
        <div class="ai-chat-actions">
            <button onclick="clearChatHistory()" title="Clear chat"><i class="fas fa-trash-alt"></i></button>
            <button onclick="toggleAiChat()" title="Close"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="ai-chat-body" id="aiChatBody">
        <div class="ai-message ai-bot">
            <div class="ai-message-content">
                👋 Hello! I'm your AI assistant. I can tell you about books, orders, customers, and revenue. Try asking me something!
            </div>
        </div>
    </div>

    <div class="ai-suggestions-wrapper">
        <div class="ai-suggestions-label">💡 Quick Questions</div>
        <div class="ai-suggestions" id="aiSuggestions">
            <button onclick="sendPrompt('How many books do we have?')">📚 Total books?</button>
            <button onclick="sendPrompt('What\'s the total revenue?')">💰 Revenue?</button>
            <button onclick="sendPrompt('Show low stock alerts')">⚠️ Low stock?</button>
            <button onclick="sendPrompt('What are the best selling books?')">🏆 Best sellers?</button>
            <button onclick="sendPrompt('How many customers do we have?')">👥 Customers?</button>
            <button onclick="sendPrompt('How many orders?')">📦 Orders?</button>
            <button onclick="sendPrompt('What categories do we have?')">📂 Categories?</button>
            <button onclick="sendPrompt('How many new customers this week?')">🆕 New customers?</button>
        </div>
    </div>

    <div class="ai-chat-footer">
        <input type="text" id="aiChatInput" placeholder="Ask me anything about your store..." onkeypress="if(event.key==='Enter')sendMessage()">
        <button onclick="sendMessage()"><i class="fas fa-arrow-up"></i></button>
    </div>
</div>