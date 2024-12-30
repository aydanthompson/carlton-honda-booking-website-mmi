const calendarEl = document.getElementById("calendar");
const availableTimesEl = document.getElementById("available-times");
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;

const DAY_NAMES = ["S", "M", "T", "W", "T", "F", "S"];
const MONTH_NAMES = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

function changeMonth(offset) {
  currentMonth += offset;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  fetchDaysWithFreeSlots();
}

function bookSlot(time) {
  const date = selectedDate.dataset.date;
  document.getElementById("selected-time").textContent = `Booking for ${date} at ${time}`;
  const bookingModal = document.getElementById("booking-modal");
  bookingModal.style.display = "block";

  document.getElementById("pay-now-button").onclick = function () {
    document.getElementById("spinner").style.display = "block";
    fetch("/online-booking/book-slot", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ date, time }),
    })
      .then((response) => response.json())
      .then((data) => {
        document.getElementById("spinner").style.display = "none";
        if (data.success) {
          document.getElementById("success-message").style.display = "block";
          alert("Slot booked successfully!");
          fetchAvailableSlots(date);
        } else {
          alert("Failed to book slot. Please try again.");
        }
      })
      .catch((error) => {
        document.getElementById("spinner").style.display = "none";
        alert("An error occurred. Please try again.");
        console.error("Error booking slot:", error);
      });
  };
}

function displayAvailableSlots(slots) {
  availableTimesEl.innerHTML = "";
  if (slots.length > 0) {
    slots.forEach((slot) => {
      const slotElement = document.createElement("button");
      slotElement.textContent = slot.time;
      slotElement.classList.add("btn", "btn-primary", "mb-2");
      slotElement.addEventListener("click", () => bookSlot(slot.time));
      availableTimesEl.appendChild(slotElement);
    });
  } else {
    availableTimesEl.textContent = "No available slots for this date.";
  }
}

function fetchAvailableSlots(date) {
  fetch(`/online-booking/get-available-slots?date=${date}`)
    .then((response) => response.json())
    .then((data) => {
      displayAvailableSlots(data);
      updateCalendarWithSlots(date, data);
    });
}

function fetchDaysWithFreeSlots() {
  fetch("/online-booking/get-days-with-free-slots")
    .then((response) => response.json())
    .then((daysWithFreeSlots) => {
      createCalendar(currentMonth, currentYear, daysWithFreeSlots);
    });
}

function handleDayClick(event) {
  const date = event.currentTarget.dataset.date;
  if (selectedDate) {
    selectedDate.classList.remove("selected");
  }
  event.currentTarget.classList.add("selected");
  selectedDate = event.currentTarget;
  fetchAvailableSlots(date);
}

function updateCalendarWithSlots(date, slots) {
  const days = document.querySelectorAll(".calendar-day");
  days.forEach((day) => {
    if (day.dataset.date === date && slots.length === 0) {
      day.classList.add("no-slots");
    } else {
      day.classList.remove("no-slots");
    }
  });
}

function createCalendar(month, year, daysWithFreeSlots = []) {
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  let calendarHTML = `
    <div class="calendar-header d-flex justify-content-between align-items-center">
      <span class="prev-button" onclick="changeMonth(-1)"><i class="fa-solid fa-caret-left"></i></span>
      <span class="month-name">${MONTH_NAMES[month]} ${year}</span>
      <span class="next-button" onclick="changeMonth(1)"><i class="fa-solid fa-caret-right"></i></span>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>`;
  for (let i = 0; i < 7; i++) {
    calendarHTML += `<th class="text-center">${DAY_NAMES[i]}</th>`;
  }
  calendarHTML += "</tr></thead><tbody><tr>";

  for (let i = 0; i < firstDay.getDay(); i++) {
    calendarHTML += "<td></td>";
  }

  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(year, month, day);
    const dateString = date.toISOString().split("T")[0];
    const isAvailable = daysWithFreeSlots.includes(dateString);
    const className = isAvailable ? "calendar-day text-center" : "calendar-day text-center greyed-out";
    calendarHTML += `<td class="${className}" data-date="${dateString}">${day}</td>`;
    if (date.getDay() === 6) {
      calendarHTML += "</tr><tr>";
    }
  }

  calendarHTML += "</tr></tbody></table>";
  calendarEl.innerHTML = calendarHTML;

  document.querySelectorAll(".calendar-day").forEach((day) => {
    if (!day.classList.contains("greyed-out")) {
      day.addEventListener("click", handleDayClick);
    }
  });
}

function initializeCalendar() {
  fetchDaysWithFreeSlots();
}

document.addEventListener("DOMContentLoaded", initializeCalendar);
