const calendarDays = document.querySelector(".calendar-days");
const monthName = document.querySelector(".calendar-header h2");
const prevBtn = document.querySelector(".prev-month");
const nextBtn = document.querySelector(".next-month");
const agendarBtn = document.getElementById("agendar-btn");
const timeSelect = document.getElementById("time-select");
const goalSelect = document.getElementById("goal-select");

let currentDate = new Date();
let selectedDay = null;

// -------------------- FUNÇÃO DO CALENDÁRIO --------------------
function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();

  const monthNames = [
    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
  ];

  monthName.textContent = `${monthNames[month]} ${year}`;
  calendarDays.innerHTML = "";

  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 1; i <= daysInMonth; i++) {
    const day = document.createElement("div");
    day.classList.add("day");
    day.innerHTML = `<span>${i}</span>`;
    calendarDays.appendChild(day);

    day.addEventListener("click", () => {
      // Remove seleção anterior
      document.querySelectorAll(".day.selected").forEach(d => {
        d.classList.remove("selected");
        const oldMarker = d.querySelector(".marker");
        if (oldMarker) oldMarker.remove();
      });

      // Marca o novo dia
      day.classList.add("selected");
      const marker = document.createElement("div");
      marker.classList.add("marker");
      day.appendChild(marker);

      // Guarda o dia selecionado
      selectedDay = i;
    });
  }
}

// -------------------- NAVEGAÇÃO DE MÊS --------------------
prevBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar(currentDate);
});

nextBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar(currentDate);
});

renderCalendar(currentDate);

// -------------------- BOTÃO AGENDAR --------------------
agendarBtn.addEventListener("click", () => {
  const horario = timeSelect.value;
  const objetivo = goalSelect.value;

  if (!selectedDay || !horario || !objetivo) {
    alert("⚠️ Por favor, selecione o dia, o horário e o objetivo antes de agendar!");
    return;
  }

  const mes = monthName.textContent;
  alert(`✅ Agendamento realizado com sucesso!\n\n📅 Dia: ${selectedDay} de ${mes}\n🕒 Horário: ${horario}\n🎯 Objetivo: ${objetivo}`);
});

    const menuIcon = document.getElementById('menu-icon');
    const sideMenu = document.getElementById('side-menu');

    menuIcon.addEventListener('click', () => {
      sideMenu.classList.toggle('active');
    });