<?php
// Chat Widget Template
?>

<!-- Chat Widget Container -->
<div id="chat-widget-container">
    <!-- Chat Icon with Notification Badge -->
    <div class="chat-icon">
        <i class="fas fa-comments"></i>
        <span class="notification-badge">1</span>
    </div>
    
    <!-- Chat Window -->
    <div class="chat-window">
        <!-- Chat Header -->
        <div class="chat-header">
            <h3>Theatre Support</h3>
            <button class="chat-close"><i class="fas fa-times"></i></button>
        </div>
        
        <!-- Chat Messages Container -->
        <div class="chat-messages">
            <!-- Sample messages will be added by JavaScript -->
        </div>
        
        <!-- Chat Input Area -->
        <div class="chat-input">
            <input type="text" placeholder="Type your message here..." disabled>
            <button disabled><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<!-- Include Chat Scripts and Styles -->
<link rel="stylesheet" href="/css/chat-widget.css">
<script src="/js/chat-widget.js"></script>

<!-- Add chat message styles -->
<style>
    #chat-widget-container .chat-messages {
        height: 300px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px;
        background-color: #f5f5f5;
    }
    
    #chat-widget-container .message-row {
        display: flex;
        width: 100%;
        margin-bottom: 10px;
        clear: both;
    }
    
    #chat-widget-container .admin-message {
        float: left;
        text-align: left;
        margin-right: auto;
    }
    
    #chat-widget-container .user-message {
        float: right;
        text-align: right;
        margin-left: auto;
    }
    
    #chat-widget-container .message-bubble {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        display: inline-block;
    }
    
    #chat-widget-container .admin-message .message-bubble {
        background-color: #e4e6eb;
        color: #333;
    }
    
    #chat-widget-container .user-message .message-bubble {
        background-color: #0084ff;
        color: white;
    }
    
    #chat-widget-container .message-info {
        font-size: 0.75rem;
        margin-top: 5px;
        opacity: 0.7;
    }
</style>

<!-- Add a script to inject sample messages to verify styling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the chat messages container
    const chatMessages = document.querySelector('#chat-widget-container .chat-messages');
    
    // Add sample admin message
    const adminMsg = document.createElement('div');
    adminMsg.className = 'message-row admin-message';
    adminMsg.innerHTML = `
        <div class="message-bubble">
            <div class="message-content">Welcome to our support chat. How can we help you today?</div>
            <div class="message-info">Admin • 10:30 AM</div>
        </div>
    `;
    chatMessages.appendChild(adminMsg);
    
    // Add sample user message
    const userMsg = document.createElement('div');
    userMsg.className = 'message-row user-message';
    userMsg.innerHTML = `
        <div class="message-bubble">
            <div class="message-content">I have a question about ticket booking.</div>
            <div class="message-info">You • 10:31 AM</div>
        </div>
    `;
    chatMessages.appendChild(userMsg);
});
</script> 