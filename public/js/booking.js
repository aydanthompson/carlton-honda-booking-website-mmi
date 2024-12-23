document.getElementById("date-picker").addEventListener("change", handleDateChange);

function handleDateChange() {
  const selectedDate = this.value;
  fetchAvailableSlots(selectedDate);
}

function fetchAvailableSlots(date) {
  fetch(`/online-booking/get-available-slots?date=${date}`)
    .then((response) => response.json())
    .then((data) => {
      displayAvailableSlots(data);
    });
}

function displayAvailableSlots(slots) {
  const availableTimesDiv = document.getElementById("available-times");
  availableTimesDiv.innerHTML = "";
  slots.forEach((slot) => {
    const slotElement = document.createElement("div");
    slotElement.textContent = slot.time;
    availableTimesDiv.appendChild(slotElement);
  });
}
