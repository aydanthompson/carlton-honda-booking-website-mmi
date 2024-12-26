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
  createCalendar(currentMonth, currentYear);
}

function displayAvailableSlots(slots) {
  availableTimesEl.innerHTML = "";
  if (slots.length > 0) {
    slots.forEach((slot) => {
      const slotElement = document.createElement("div");
      slotElement.textContent = slot.time;
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

function createCalendar(month, year) {
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  let calendarHTML = `
    <div class="calendar-header d-flex justify-content-between align-items-center">
      <span class="prev-button" onclick="changeMonth(-1)">&lt;</span>
      <span class="month-name">${MONTH_NAMES[month]} ${year}</span>
      <span class="next-button" onclick="changeMonth(1)">&gt;</span>
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
    calendarHTML += `<td class="calendar-day text-center" data-date="${date.toISOString().split("T")[0]}">${day}</td>`;
    if (date.getDay() === 6) {
      calendarHTML += "</tr><tr>";
    }
  }

  calendarHTML += "</tr></tbody></table>";
  calendarEl.innerHTML = calendarHTML;

  document.querySelectorAll(".calendar-day").forEach((day) => {
    day.addEventListener("click", handleDayClick);
  });
}

function initializeCalendar() {
  createCalendar(currentMonth, currentYear);
}

document.addEventListener("DOMContentLoaded", initializeCalendar);
