/**
 * Chat Widget for Theatre Booking System
 * Allows users to chat with support staff
 */
document.addEventListener("DOMContentLoaded", function () {
  // Variables
  let chatSession = null;
  let lastMessageTimestamp = 0;
  let messagePollingInterval = null;
  let unreadCount = 0;

  // DOM Elements
  const chatIcon = document.querySelector(".chat-icon");
  const chatWindow = document.querySelector(".chat-window");
  const chatClose = document.querySelector(".chat-close");
  const chatBody = document.querySelector(".chat-body");
  const chatInput = document.querySelector(".chat-input");
  const chatSend = document.querySelector(".chat-send");
  const chatNotification = document.querySelector(".chat-notification");

  // Event Listeners
  chatIcon.addEventListener("click", toggleChat);
  chatClose.addEventListener("click", closeChat);
  chatSend.addEventListener("click", sendMessage);
  chatInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  // Initialize chat when widget is first opened
  function initChat() {
    if (chatSession !== null) return;

    fetch("/includes/chat_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "action=init",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          chatSession = data.session_id;
          lastMessageTimestamp = Math.floor(Date.now() / 1000);

          // Add welcome message
          if (data.welcome_message) {
            addMessage(data.welcome_message, "support");
          }

          // Start polling for new messages
          startMessagePolling();
        } else {
          console.error("Failed to initialize chat:", data.message);
          addSystemMessage(
            "Error connecting to support. Please try again later."
          );
        }
      })
      .catch((error) => {
        console.error("Error initializing chat:", error);
        addSystemMessage(
          "Error connecting to support. Please try again later."
        );
      });
  }

  // Toggle chat window visibility
  function toggleChat() {
    chatWindow.classList.toggle("active");

    if (chatWindow.classList.contains("active")) {
      // Initialize chat session if none exists
      if (chatSession === null) {
        initChat();
      }

      // Reset unread count
      unreadCount = 0;
      updateNotificationBadge();

      // Focus input
      chatInput.focus();
    }
  }

  // Close chat window
  function closeChat() {
    chatWindow.classList.remove("active");
  }

  // Send message to server
  function sendMessage() {
    const message = chatInput.value.trim();

    if (message === "" || chatSession === null) return;

    // Add message to chat immediately
    addMessage(message, "user");

    // Clear input
    chatInput.value = "";

    // Send message to server
    fetch("/includes/chat_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=send&session_id=${chatSession}&message=${encodeURIComponent(
        message
      )}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          console.error("Failed to send message:", data.message);
          addSystemMessage("Error sending message. Please try again.");
        }
      })
      .catch((error) => {
        console.error("Error sending message:", error);
        addSystemMessage("Error sending message. Please try again.");
      });
  }

  // Add message to chat window
  function addMessage(message, sender) {
    const messageElement = document.createElement("div");
    messageElement.classList.add("chat-message", sender);
    messageElement.textContent = message;

    const timestamp = document.createElement("span");
    timestamp.classList.add("chat-timestamp");
    timestamp.textContent = new Date().toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });

    messageElement.appendChild(timestamp);
    chatBody.appendChild(messageElement);

    // Scroll to bottom
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  // Add system message to chat
  function addSystemMessage(message) {
    const messageElement = document.createElement("div");
    messageElement.classList.add("chat-message", "system");
    messageElement.style.backgroundColor = "#fff3cd";
    messageElement.style.color = "#856404";
    messageElement.style.textAlign = "center";
    messageElement.style.float = "none";
    messageElement.style.margin = "10px auto";
    messageElement.textContent = message;

    chatBody.appendChild(messageElement);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  // Start polling for new messages
  function startMessagePolling() {
    if (messagePollingInterval !== null) return;

    messagePollingInterval = setInterval(fetchNewMessages, 5000);
  }

  // Stop polling for new messages
  function stopMessagePolling() {
    if (messagePollingInterval === null) return;

    clearInterval(messagePollingInterval);
    messagePollingInterval = null;
  }

  // Fetch new messages from server
  function fetchNewMessages() {
    if (chatSession === null) return;

    fetch("/includes/chat_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=getNew&session_id=${chatSession}&last_timestamp=${lastMessageTimestamp}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.messages && data.messages.length > 0) {
          // Process new messages
          data.messages.forEach((msg) => {
            if (msg.sender !== "user") {
              addMessage(msg.message, "support");

              // Increment unread count if chat window is not active
              if (!chatWindow.classList.contains("active")) {
                unreadCount++;
                updateNotificationBadge();
              }
            }

            // Update the last timestamp
            if (msg.timestamp > lastMessageTimestamp) {
              lastMessageTimestamp = msg.timestamp;
            }
          });
        }
      })
      .catch((error) => {
        console.error("Error fetching new messages:", error);
      });
  }

  // Update notification badge
  function updateNotificationBadge() {
    if (unreadCount > 0) {
      chatNotification.textContent = unreadCount > 9 ? "9+" : unreadCount;
      chatNotification.classList.add("active");
    } else {
      chatNotification.classList.remove("active");
    }
  }

  // Handle page unload - close session
  window.addEventListener("beforeunload", function () {
    if (chatSession !== null) {
      // Using navigator.sendBeacon for reliable delivery even during page unload
      navigator.sendBeacon(
        "/includes/chat_ajax.php",
        new URLSearchParams({
          action: "close",
          session_id: chatSession,
        })
      );

      stopMessagePolling();
    }
  });
});
