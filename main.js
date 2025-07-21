// js/main.js
document.addEventListener("DOMContentLoaded", () => {
  const eventsContainer = document.getElementById("events-container");

  fetch("mockEvents.json")
    .then(res => res.json())
    .then(data => {
      data.forEach(event => {
        const card = document.createElement("div");
        card.className = "event-card";
        card.innerHTML = `
          <div class="event-title">${event.title}</div>
          <div class="event-meta">${event.date} • ${event.location}</div>
          <div class="event-meta"><strong>Club:</strong> ${event.club}</div>
          <button class="register-btn" onclick="alert('Registering for ${event.title}')">Register</button>
        `;
        eventsContainer.appendChild(card);
      });
    })
    .catch(err => {
      eventsContainer.innerHTML = "<p>Failed to load events 😓</p>";
      console.error(err);
    });
});
