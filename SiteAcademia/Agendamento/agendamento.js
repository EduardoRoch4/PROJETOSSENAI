const calendarDays = document.querySelector(".calendar-days");
const monthName = document.querySelector(".calendar-header h2");
const prevBtn = document.querySelector(".prev-month");
const nextBtn = document.querySelector(".next-month");

let currentDate = new Date();

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();

  const monthNames = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho",
                      "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
  monthName.textContent = `${monthNames[month]} ${year}`;

  calendarDays.innerHTML = "";

  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for(let i = 1; i <= daysInMonth; i++){
    const day = document.createElement("div");
    day.classList.add("day");
    day.innerHTML = `<span>${i}</span>`;
    calendarDays.appendChild(day);

    day.addEventListener("click", () => {
      document.querySelectorAll(".day.selected").forEach(d => {
        d.classList.remove("selected");
        const oldMarker = d.querySelector(".marker");
        if(oldMarker) oldMarker.remove();
      });

      day.classList.add("selected");
      const marker = document.createElement("div");
      marker.classList.add("marker");
      day.appendChild(marker);
    });
  }
}

prevBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar(currentDate);
});

nextBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar(currentDate);
});

renderCalendar(currentDate);