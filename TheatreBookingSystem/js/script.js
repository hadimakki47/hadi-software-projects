document.addEventListener("DOMContentLoaded", function () {
  // Seat selection
  const seatMap = document.querySelector(".seat-map");
  if (seatMap) {
    seatMap.addEventListener("click", function (e) {
      if (
        e.target.classList.contains("seat") &&
        !e.target.classList.contains("booked")
      ) {
        e.target.classList.toggle("selected");
        updateSelectedSeats();
      }
    });
  }

  // Update selected seats and total price
  function updateSelectedSeats() {
    const selectedSeats = document.querySelectorAll(".seat.selected");
    const selectedSeatsDisplay = document.getElementById("selected-seats");
    const totalPriceDisplay = document.getElementById("total-price");
    const selectedSeatsInput = document.getElementById("selected-seats-input");

    if (selectedSeatsDisplay) {
      let seatsText = "";
      let seatIds = [];

      selectedSeats.forEach(function (seat) {
        seatsText += seat.getAttribute("data-seat") + ", ";
        seatIds.push(seat.getAttribute("data-id"));
      });

      // Remove trailing comma and space
      seatsText = seatsText.replace(/, $/, "");

      selectedSeatsDisplay.textContent = seatsText || "None";

      // Update hidden input with selected seat IDs
      if (selectedSeatsInput) {
        selectedSeatsInput.value = JSON.stringify(seatIds);
      }

      // Calculate and update total price if applicable
      if (totalPriceDisplay) {
        let totalPrice = 0;
        selectedSeats.forEach(function (seat) {
          totalPrice += parseFloat(seat.getAttribute("data-price"));
        });

        totalPriceDisplay.textContent = totalPrice.toFixed(2);
      }
    }
  }
});
