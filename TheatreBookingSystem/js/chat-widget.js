/**
 * Theatre Chat Widget
 * Provides customer support chat functionality on all pages of the theatre website
 */

// Global variables
let chatSession = null;
let lastMessageTimestamp = 0;
let pollingInterval = null;
let unreadCount = 0;

// Update the API endpoint URL
const CHAT_API_URL = "/standalone_chat.php";

// Check if current page is an admin page
function isAdminPage() {
  return window.location.pathname.includes("/admin/");
}

// Initialize when DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Don't initialize chat widget on admin pages
  if (isAdminPage()) {
    console.log("Chat widget disabled on admin pages");
    return;
  }

  // Check if chat widget container exists
  const chatContainer = document.getElementById("chat-widget-container");
  if (!chatContainer) return;

  // Get DOM elements
  const chatIcon = document.querySelector(".chat-icon");
  const chatWindow = document.querySelector(".chat-window");
  const chatClose = document.querySelector(".chat-close");
  const chatInput = document.querySelector(".chat-input input");
  const chatSendBtn = document.querySelector(".chat-input button");
  const notificationBadge = document.querySelector(".notification-badge");

  // Add event listeners
  chatIcon.addEventListener("click", toggleChat);
  chatClose.addEventListener("click", closeChat);
  chatSendBtn.addEventListener("click", sendMessage);

  // Send message on Enter key
  chatInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      sendMessage();
    }
  });

  // Handle page unload - use sendBeacon for reliable delivery
  window.addEventListener("beforeunload", function () {
    if (chatSession) {
      const data = new FormData();
      data.append("action", "close");
      data.append("session_id", chatSession);
      navigator.sendBeacon(CHAT_API_URL, data);
    }
  });
});

/**
 * Initialize the chat
 * @param {number} retryCount - Number of retry attempts (default: 0)
 */
function initChat(retryCount = 0) {
  const MAX_RETRIES = 3;

  // Create form data for the request
  const formData = new FormData();
  formData.append("action", "init");

  // Show loading message only on first attempt
  if (retryCount === 0) {
    addSystemMessage("Connecting to support system...");
  }

  // Send request to initialize chat session
  fetch(CHAT_API_URL, {
    method: "POST",
    body: formData,
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then(async (response) => {
      if (!response.ok) {
        // Try to get error message from response if possible
        let errorMessage = `Server error: ${response.status}`;
        try {
          const errorData = await response.text();
          console.log("Server response:", errorData);
          errorMessage = errorData;
        } catch (e) {
          // If we can't parse the error, just use the status
        }
        throw new Error(errorMessage);
      }

      // Check if response is empty
      const text = await response.text();
      if (!text || text.trim() === "") {
        throw new Error("Server returned empty response");
      }

      // Try to parse JSON
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error("Invalid JSON response:", text);
        throw new Error("Server returned invalid JSON");
      }
    })
    .then((data) => {
      if (data.success) {
        // Remove loading message
        clearSystemMessages();

        chatSession = data.session_id;

        // Add welcome message
        if (data.welcome_message) {
          addMessage("support", data.welcome_message);
        }

        // Start polling for new messages
        startMessagePolling();
      } else {
        throw new Error(data.message || "Failed to initialize chat");
      }
    })
    .catch((error) => {
      console.error("Chat initialization error:", error);

      // If we haven't exceeded max retries and the error suggests a connection issue, try again
      if (
        retryCount < MAX_RETRIES &&
        (error.message.includes("connect") ||
          error.message.includes("empty response") ||
          error.message.includes("invalid JSON"))
      ) {
        // Add retry message if not first attempt
        if (retryCount > 0) {
          addSystemMessage(
            `Retrying connection... (${retryCount}/${MAX_RETRIES})`
          );
        }

        // Wait longer between each retry
        const delay = 1000 * (retryCount + 1);

        setTimeout(() => {
          initChat(retryCount + 1);
        }, delay);
      } else {
        // Max retries exceeded or not a connection issue
        clearSystemMessages();
        addSystemMessage(
          `Failed to connect to chat service: ${error.message}. Please try again later.`
        );

        // Add retry button
        addRetryButton();
      }
    });
}

/**
 * Clear all system messages
 */
function clearSystemMessages() {
  const messagesContainer = document.querySelector(".chat-messages");
  const systemMessages = messagesContainer.querySelectorAll(".system-message");
  systemMessages.forEach((msg) => msg.remove());
}

/**
 * Add a retry button to reconnect
 */
function addRetryButton() {
  const messagesContainer = document.querySelector(".chat-messages");
  const retryDiv = document.createElement("div");
  retryDiv.className = "retry-button";
  retryDiv.innerHTML =
    '<button class="btn btn-primary btn-sm">Retry Connection</button>';

  // Add click handler
  retryDiv.querySelector("button").addEventListener("click", function () {
    // Remove all system messages and the retry button
    clearSystemMessages();
    this.parentNode.remove();

    // Try to connect again
    initChat();
  });

  messagesContainer.appendChild(retryDiv);
}

/**
 * Toggle chat window visibility
 */
function toggleChat() {
  // Don't try to toggle chat on admin pages
  if (isAdminPage()) return;

  const chatWindow = document.querySelector(".chat-window");
  if (!chatWindow) {
    console.error("Chat window element not found");
    return;
  }

  if (chatWindow.classList.contains("active")) {
    closeChat();
  } else {
    chatWindow.classList.add("active");

    // Reset unread count
    unreadCount = 0;
    updateNotificationBadge();

    // Focus the input field
    const inputField = document.querySelector(".chat-input input");
    if (inputField) {
      inputField.focus();
    }

    // Initialize chat if not already done
    if (!chatSession) {
      initChat();
    }
  }
}

/**
 * Close the chat window and notify server
 */
function closeChat() {
  const chatWindow = document.querySelector(".chat-window");
  chatWindow.classList.remove("active");

  // If there's an active chat session, tell the server we're closing it
  if (chatSession) {
    const formData = new FormData();
    formData.append("action", "close");
    formData.append("session_id", chatSession);

    fetch(CHAT_API_URL, {
      method: "POST",
      body: formData,
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    }).catch((error) => {
      console.error("Error closing chat:", error);
    });
  }
}

/**
 * Send a message
 */
function sendMessage() {
  const input = document.querySelector(".chat-input input");
  const message = input.value.trim();

  if (!message || !chatSession) return;

  // Add message to the chat immediately
  addMessage("user", message);

  // Clear input
  input.value = "";

  // Create form data for the request
  const formData = new FormData();
  formData.append("action", "send");
  formData.append("session_id", chatSession);
  formData.append("message", message);

  // Send message to server
  fetch(CHAT_API_URL, {
    method: "POST",
    body: formData,
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        addSystemMessage("Failed to send message. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error sending message:", error);
      addSystemMessage("Failed to send message. Please check your connection.");
    });
}

/**
 * Add a message to the chat window
 */
function addMessage(sender, text, timestamp = null) {
  const messagesContainer = document.querySelector(".chat-messages");
  const messageElement = document.createElement("div");

  // Set message class based on sender - support messages come from admin
  const messageType = sender === "support" ? "admin" : "user";
  messageElement.className = `message ${messageType}-message`;

  // Create message content
  const messageText = document.createElement("div");
  messageText.className = "message-text";
  messageText.textContent = text;
  messageElement.appendChild(messageText);

  // Add timestamp if provided
  if (timestamp) {
    const messageTime = document.createElement("div");
    messageTime.className = "message-time";

    const date = new Date(timestamp * 1000);
    messageTime.textContent = date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });

    messageElement.appendChild(messageTime);
  }

  // Add to messages container
  messagesContainer.appendChild(messageElement);

  // Scroll to bottom
  messagesContainer.scrollTop = messagesContainer.scrollHeight;

  // Update unread count if chat window is not active and message is from admin
  if (
    (sender === "support" || sender === "admin") &&
    !document.querySelector(".chat-window").classList.contains("active")
  ) {
    unreadCount++;
    updateNotificationBadge();
  }
}

/**
 * Add a system message to the chat window
 */
function addSystemMessage(text) {
  // Don't try to add messages on admin pages
  if (isAdminPage()) return;

  const messagesContainer = document.querySelector(".chat-messages");
  if (!messagesContainer) {
    console.error("Chat messages container not found");
    return;
  }

  const messageElement = document.createElement("div");
  messageElement.className = "message system-message";

  // Create message content with icon
  const messageContent = document.createElement("div");
  messageContent.className = "message-text";
  messageContent.innerHTML = `<i class="fas fa-info-circle"></i> ${text}`;
  messageElement.appendChild(messageContent);

  messagesContainer.appendChild(messageElement);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

/**
 * Start polling for new messages
 */
function startMessagePolling() {
  // Clear any existing interval
  if (pollingInterval) {
    clearInterval(pollingInterval);
  }

  // Function to fetch new messages
  fetchNewMessages();

  // Set up polling interval (every 3 seconds)
  pollingInterval = setInterval(fetchNewMessages, 3000);
}

/**
 * Stop polling for new messages
 */
function stopMessagePolling() {
  if (pollingInterval) {
    clearInterval(pollingInterval);
    pollingInterval = null;
  }
}

/**
 * Fetch new messages from the server
 * @param {number} failureCount - Number of consecutive failures (default: 0)
 */
function fetchNewMessages(failureCount = 0) {
  const MAX_FAILURES = 5;

  if (!chatSession) return;

  const formData = new FormData();
  formData.append("action", "getNew");
  formData.append("session_id", chatSession);
  formData.append("last_timestamp", lastMessageTimestamp);

  fetch(CHAT_API_URL, {
    method: "POST",
    body: formData,
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then(async (response) => {
      if (!response.ok) {
        throw new Error(`Server error: ${response.status}`);
      }

      // Check if response is empty
      const text = await response.text();
      if (!text || text.trim() === "") {
        throw new Error("Server returned empty response");
      }

      // Try to parse JSON
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error("Invalid JSON response:", text);
        throw new Error("Server returned invalid JSON");
      }
    })
    .then((data) => {
      if (data.success && data.messages && data.messages.length > 0) {
        // Add each new message to the chat
        data.messages.forEach((msg) => {
          addMessage(msg.sender, msg.message, msg.timestamp);

          // Update the last message timestamp
          if (msg.timestamp > lastMessageTimestamp) {
            lastMessageTimestamp = msg.timestamp;
          }
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching messages:", error);
      // Don't show error messages for polling failures to avoid cluttering the chat
      // But do add a reconnect button if disconnected for too long
      if (
        error.message.includes("Server returned empty response") ||
        error.message.includes("Server returned invalid JSON")
      ) {
        addSystemMessage("Connection lost. Attempting to reconnect...");

        // Stop the current polling and try to reconnect after a delay
        stopMessagePolling();
        setTimeout(() => {
          clearSystemMessages();
          startMessagePolling();
        }, 5000);
      }
    });
}

/**
 * Update the notification badge with unread count
 */
function updateNotificationBadge() {
  // Don't try to update badge on admin pages
  if (isAdminPage()) return;

  const badge = document.querySelector(".notification-badge");
  if (!badge) {
    console.error("Notification badge element not found");
    return;
  }

  if (unreadCount > 0) {
    badge.textContent = unreadCount > 9 ? "9+" : unreadCount;
    badge.classList.add("active");
  } else {
    badge.classList.remove("active");
  }
}

// Function to handle common AJAX errors and show in chat window
function handleAjaxError(error) {
  console.error("Chat error:", error);
  const errorMsg = error.responseText
    ? error.responseText
    : "Connection error. Please try again.";

  // Get the messages container
  const chatMessages = document.querySelector(".chat-messages");

  // Display error in chat window
  const messageElement = document.createElement("div");
  messageElement.className = "chat-message system-message";
  messageElement.innerHTML = `<div class="message-content error">Error: ${errorMsg}</div>`;
  chatMessages.appendChild(messageElement);
  chatMessages.scrollTop = chatMessages.scrollHeight;

  // Update status
  updateChatStatus("Connection error");
}

// Update chat status display
function updateChatStatus(status) {
  // Don't try to update status on admin pages
  if (isAdminPage()) return;

  const statusElement = document.getElementById("chat-status");
  if (statusElement) {
    statusElement.textContent = status;
  } else {
    // Create status element if it doesn't exist
    const newStatusElement = document.createElement("div");
    newStatusElement.id = "chat-status";
    newStatusElement.className = "chat-status";
    newStatusElement.textContent = status;

    // Find the chat input and insert the status element before it
    const chatInput = document.querySelector(".chat-input");
    if (chatInput) {
      chatInput.before(newStatusElement);
    } else {
      console.error("Chat input element not found");
    }
  }
}
