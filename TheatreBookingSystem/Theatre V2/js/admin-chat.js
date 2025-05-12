/**
 * Admin Chat Interface Enhancement
 */
document.addEventListener("DOMContentLoaded", function () {
  // Ensure all admin messages are properly labeled
  const adminMessages = document.querySelectorAll(".admin-message .sender");
  adminMessages.forEach(function (element) {
    // Force admin message sender to show "Admin"
    element.innerHTML = '<i class="fas fa-user-shield"></i> Admin';
  });

  // Add smooth scrolling to newest messages
  const chatMessages = document.querySelector(".chat-messages");
  if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Add focus to message input on page load
  const messageInput = document.getElementById("message");
  if (messageInput) {
    messageInput.focus();
  }
});
