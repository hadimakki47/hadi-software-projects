/**
 * Support Chat Widget
 * Provides a floating chat widget for customer support
 */
document.addEventListener("DOMContentLoaded", function () {
  // Create the widget DOM structure if it doesn't exist yet
  if (!document.querySelector(".support-widget")) {
    initializeSupportWidget();
  }

  // Variables for chat state
  let isOpen = false;
  let lastMessageId = 0;
  let chatInterval = null;

  // Initialize the widget and attach event listeners
  function initializeSupportWidget() {
    // Create widget container
    const widget = document.createElement("div");
    widget.className = "support-widget";

    // Create chat button
    const button = document.createElement("button");
    button.className = "support-widget__button";
    button.innerHTML = '<i class="fas fa-comments"></i>';
    button.setAttribute("aria-label", "Open support chat");

    // Create notification badge
    const notification = document.createElement("span");
    notification.className = "support-widget__notification hidden";
    notification.textContent = "0";

    // Create chat popup
    const popup = document.createElement("div");
    popup.className = "support-widget__popup hidden";

    // Create header
    const header = document.createElement("div");
    header.className = "support-widget__header";
    header.innerHTML = `
        <h3>Support Chat</h3>
        <button class="support-widget__close" aria-label="Close chat">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Create body
    const body = document.createElement("div");
    body.className = "support-widget__body";

    // Create messages container
    const messages = document.createElement("div");
    messages.className = "support-widget__messages";

    // Create form
    const form = document.createElement("form");
    form.className = "support-widget__form";
    form.innerHTML = `
        <input type="text" placeholder="Type your message..." required>
        <button type="submit" aria-label="Send message">
            <i class="fas fa-paper-plane"></i>
        </button>
    `;

    // Assemble the widget
    body.appendChild(messages);
    body.appendChild(form);
    popup.appendChild(header);
    popup.appendChild(body);
    button.appendChild(notification);
    widget.appendChild(button);
    widget.appendChild(popup);

    // Add to document
    document.body.appendChild(widget);

    // Add event listeners
    button.addEventListener("click", toggleChat);
    header
      .querySelector(".support-widget__close")
      .addEventListener("click", toggleChat);
    form.addEventListener("submit", sendMessage);

    // Check for new messages periodically when the chat is open
    setInterval(() => {
      if (!popup.classList.contains("hidden")) {
        fetchMessages();
      }
    }, 10000); // Check every 10 seconds
  }

  // Function to toggle chat window
  function toggleChat() {
    const popup = document.querySelector(".support-widget__popup");
    const notification = document.querySelector(
      ".support-widget__notification"
    );

    popup.classList.toggle("hidden");
    isOpen = !popup.classList.contains("hidden");

    if (isOpen) {
      // Reset notification when opening the chat
      notification.classList.add("hidden");
      notification.textContent = "0";

      // Focus the input field
      setTimeout(() => {
        document.querySelector(".support-widget__form input").focus();
      }, 300);

      // If this is the first time opening, start a chat session
      if (!sessionStorage.getItem("chatSessionId")) {
        startChat();
      } else {
        fetchMessages();
      }
    }
  }

  // Function to start a chat
  function startChat() {
    const messages = document.querySelector(".support-widget__messages");
    messages.innerHTML =
      '<div class="support-widget__message support-widget__message--support"><div class="support-widget__message-content">Welcome to our support chat! How can we help you today?</div><div class="support-widget__message-time">Just now</div></div>';

    // Send request to start chat session
    fetch("/includes/chat_handler.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "action=start_chat",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          sessionStorage.setItem("chatSessionId", data.session_id);
        }
      })
      .catch((error) => {
        console.error("Error starting chat:", error);
      });
  }

  // Function to send a message
  function sendMessage(event) {
    event.preventDefault();

    const input = document.querySelector(".support-widget__form input");
    const message = input.value.trim();

    if (!message) return;

    const messages = document.querySelector(".support-widget__messages");
    const sessionId = sessionStorage.getItem("chatSessionId");

    // Add message to UI immediately
    const now = new Date();
    const timeStr =
      now.getHours().toString().padStart(2, "0") +
      ":" +
      now.getMinutes().toString().padStart(2, "0");

    messages.innerHTML += `
        <div class="support-widget__message support-widget__message--user">
            <div class="support-widget__message-content">${escapeHtml(
              message
            )}</div>
            <div class="support-widget__message-time">${timeStr}</div>
        </div>
    `;

    // Clear input
    input.value = "";

    // Scroll to bottom
    messages.scrollTop = messages.scrollHeight;

    // Send to server
    const widgetHTML = `
      <div class="support-widget">
        <button class="support-widget__button" aria-label="Support Chat">
          <i class="fas fa-comments"></i>
          <span class="support-widget__notification hidden">0</span>
        </button>
        <div class="support-widget__popup hidden">
          <div class="support-widget__header">
            <h3>Support Chat</h3>
            <button class="support-widget__close" aria-label="Close chat">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="support-widget__body">
            <div class="support-widget__messages"></div>
            <form class="support-widget__form">
              <input type="text" placeholder="Type your message..." required>
              <button type="submit" aria-label="Send message">
                <i class="fas fa-paper-plane"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    `;

    // Insert widget into DOM
    document.body.insertAdjacentHTML("beforeend", widgetHTML);

    // Add event listeners
    const widget = document.querySelector(".support-widget");
    const button = widget.querySelector(".support-widget__button");
    const closeBtn = widget.querySelector(".support-widget__close");
    const form = widget.querySelector(".support-widget__form");

    // Toggle chat window
    button.addEventListener("click", toggleChat);

    // Close chat window
    closeBtn.addEventListener("click", toggleChat);

    // Handle message submission
    form.addEventListener("submit", sendMessage);
  }

  // Function to toggle chat window
  function toggleChat() {
    const popup = document.querySelector(".support-widget__popup");
    const notification = document.querySelector(
      ".support-widget__notification"
    );
    isOpen = !isOpen;

    if (isOpen) {
      popup.classList.remove("hidden");
      notification.classList.add("hidden");
      notification.textContent = "0";

      // Start fetching messages
      startChat();
      chatInterval = setInterval(fetchMessages, 5000);
    } else {
      popup.classList.add("hidden");

      // Stop fetching messages
      if (chatInterval) {
        clearInterval(chatInterval);
        chatInterval = null;
      }
    }
  }

  // Function to start a chat
  function startChat() {
    fetch("/includes/chat_handler.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "action=start_chat",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.messages) {
          displayMessages(data.messages);
          // Set the last message ID
          if (data.messages.length > 0) {
            lastMessageId = data.messages[data.messages.length - 1].id;
          }
        }
      })
      .catch((error) => {
        console.error("Error starting chat:", error);
      });
  }

  // Function to send a message
  function sendMessage(event) {
    event.preventDefault();

    const inputField = document.querySelector(".support-widget__form input");
    const message = inputField.value.trim();

    if (!message) return;

    // Clear the input field
    inputField.value = "";

    fetch("/includes/chat_handler.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=send_message&message=${encodeURIComponent(message)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Add the message to the chat
          const messagesContainer = document.querySelector(
            ".support-widget__messages"
          );
          const messageHTML = `
          <div class="support-widget__message support-widget__message--user">
            <div class="support-widget__message-content">${message}</div>
            <div class="support-widget__message-time">${data.time}</div>
          </div>
        `;
          messagesContainer.insertAdjacentHTML("beforeend", messageHTML);

          // Scroll to the bottom
          messagesContainer.scrollTop = messagesContainer.scrollHeight;

          // Fetch new messages (including support response if any)
          setTimeout(fetchMessages, 1000);
        }
      })
      .catch((error) => {
        console.error("Error sending message:", error);
      });
  }

  // Function to fetch new messages
  function fetchMessages() {
    fetch("/includes/chat_handler.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=get_messages&last_id=${lastMessageId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.messages && data.messages.length > 0) {
          displayMessages(data.messages);

          // Update last message ID
          lastMessageId = data.messages[data.messages.length - 1].id;

          // Show notification for new support messages if chat is closed
          if (!isOpen) {
            const notification = document.querySelector(
              ".support-widget__notification"
            );
            const supportMessages = data.messages.filter(
              (msg) => msg.is_support
            );
            if (supportMessages.length > 0) {
              notification.textContent =
                parseInt(notification.textContent || 0) +
                supportMessages.length;
              notification.classList.remove("hidden");
            }
          }
        }
      })
      .catch((error) => {
        console.error("Error fetching messages:", error);
      });
  }

  // Function to display messages
  function displayMessages(messages) {
    const messagesContainer = document.querySelector(
      ".support-widget__messages"
    );

    // Add each message to the chat
    messages.forEach((message) => {
      const messageClass = message.is_support
        ? "support-widget__message--support"
        : "support-widget__message--user";
      const messageHTML = `
        <div class="support-widget__message ${messageClass}">
          <div class="support-widget__message-content">${message.message}</div>
          <div class="support-widget__message-time">${message.time}</div>
        </div>
      `;
      messagesContainer.insertAdjacentHTML("beforeend", messageHTML);
    });

    // Scroll to the bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
});
