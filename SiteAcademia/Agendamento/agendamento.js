const calendarDays = document.querySelector(".calendar-days");
const monthName = document.querySelector(".calendar-header h2");
const prevBtn = document.querySelector(".prev-month");
const nextBtn = document.querySelector(".next-month");
const agendarBtn = document.getElementById("agendar-btn");
const timeSelect = document.getElementById("time-select");
const goalSelect = document.getElementById("goal-select");
const modalidadeSelect = document.getElementById("modalidade-select");

let currentDate = new Date();
let selectedDay = null;

// -------------------- FUNÇÃO DO CALENDÁRIO --------------------
function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth(); // 0 (Jan) a 11 (Dez)

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
      });

      // Marca o novo dia
      day.classList.add("selected");
      selectedDay = i; // Armazena o dia (número)
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

// -------------------- BOTÃO AGENDAR (MODIFICADO) --------------------
agendarBtn.addEventListener("click", async () => {
  const horario = timeSelect.value;
  const objetivo = goalSelect.value;
  const modalidade = modalidadeSelect ? modalidadeSelect.value : '';

  // 1. Validação no front-end
  if (!selectedDay || !horario || !objetivo || !modalidade) {
    alert("⚠️ Por favor, selecione o dia, o horário, o objetivo e a modalidade antes de agendar!");
    return;
  }

  // 2. Preparar dados para enviar
  const dadosAgendamento = {
    dia: selectedDay,
    mes: currentDate.getMonth() + 1, // JS (0-11) -> PHP (1-12)
    ano: currentDate.getFullYear(),
    horario: horario,
    objetivo: objetivo
    ,modalidade: modalidade
  };

  // 3. Enviar dados para o PHP (processar_agendamento.php)
  try {
    const response = await fetch('processar_agendamento.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(dadosAgendamento)
    });

    const text = await response.text();
    // Tentar interpretar como JSON; se não for JSON, mostrar o texto bruto para depuração
    let resultado;
    try {
      resultado = JSON.parse(text);
    } catch (parseError) {
      console.error('Resposta do servidor (não JSON):', text);
      alert('❌ Resposta inválida do servidor. Veja console para mais detalhes.\nResposta bruta:\n' + text);
      return;
    }

    // 4. Mostrar a resposta do PHP (seja sucesso ou erro)
    alert(resultado.message || ('Servidor retornou: ' + text));

    if (resultado.status === 'success') {
      // Opcional: limpar seleção após sucesso
      selectedDay = null;
      timeSelect.value = "";
      goalSelect.value = "";
      modalidadeSelect.value = "";
      renderCalendar(currentDate);
    }

  } catch (error) {
    console.error("Erro no fetch:", error);
    alert("❌ Ocorreu um erro ao enviar sua solicitação. Verifique o console para mais detalhes.\n" + (error && error.message ? error.message : ''));
  }
});

// -------------------- LÓGICA DO MENU HAMBÚRGUER --------------------
// (Esta parte estava no seu JS original, mas com um erro de sintaxe)
const menuIcon = document.getElementById('menu-icon');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn'); // Adicionado
const overlay = document.getElementById('overlay');     // Adicionado

menuIcon.addEventListener('click', () => {
  sideMenu.classList.add('active');
  overlay.style.display = 'block'; // Mostra o overlay
});

closeBtn.addEventListener('click', () => {
  sideMenu.classList.remove('active');
  overlay.style.display = 'none'; // Esconde o overlay
});

overlay.addEventListener('click', () => {
  sideMenu.classList.remove('active');
  overlay.style.display = 'none'; // Esconde o overlay
});

const fadeElements = document.querySelectorAll('.fade-in-up');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.2 });
    fadeElements.forEach(el => observer.observe(el));

// --- Atualiza UI de sessão (perfil, login/logout, mostrar/ocultar Painel Admin) ---
async function refreshSessionUI() {
  try {
    const r = await fetch('../Login/session_status.php');
    const s = await r.json();
    const isLogged = !!s.logged;
    const perfil = s.perfil || null;

    const loginBtn = document.getElementById('login-btn');
    const perfilBtn = document.getElementById('perfil-btn');
    const loginSide = document.getElementById('login-side');
    const perfilSide = document.getElementById('perfil-side');

    if (loginBtn) {
      loginBtn.href = isLogged ? '/Login/login.php?acao=logout' : '/Login/login.php';
      loginBtn.textContent = isLogged ? 'Logout' : 'Login';
    }
    if (loginSide) {
      loginSide.href = isLogged ? '/Login/login.php?acao=logout' : '/Login/login.php';
      loginSide.textContent = isLogged ? 'Logout' : 'Login';
    }

    if (perfilBtn) perfilBtn.style.display = isLogged ? '' : 'none';
    if (perfilSide) perfilSide.style.display = isLogged ? '' : 'none';

    // show logged user name
    const userNameEl = document.getElementById('user-name');
    const userDisplay = document.getElementById('user-display');
    const userNameSide = document.getElementById('user-name-side');
    const userDisplaySide = document.getElementById('user-display-side');
    if (userNameEl) userNameEl.textContent = s.usuario || '';
    if (userDisplay) userDisplay.style.display = isLogged ? '' : 'none';
    if (userNameSide) userNameSide.textContent = s.usuario || '';
    if (userDisplaySide) userDisplaySide.style.display = isLogged ? '' : 'none';

    // Inject/remove admin links so only admins see the button
    function setAdminLinks(show) {
      const nav = document.querySelector('.nav-buttons') || document.getElementById('nav-buttons');
      if (nav) {
        let a = nav.querySelector('a[data-admin-link]');
        if (show && !a) {
          a = document.createElement('a');
          a.href = '/Admin/painel.php';
          a.textContent = 'Painel Admin';
          a.setAttribute('data-admin-link', '1');
          nav.appendChild(a);
        }
        if (!show && a) a.remove();
      }

      const side = document.getElementById('side-menu') || document.querySelector('.side-menu');
      if (side) {
        let s = side.querySelector('a[data-admin-link-side]');
        if (show && !s) {
          s = document.createElement('a');
          s.href = '/Admin/painel.php';
          s.textContent = 'Painel Admin';
          s.setAttribute('data-admin-link-side', '1');
          side.appendChild(s);
        }
        if (!show && s) s.remove();
      }
    }

    setAdminLinks(perfil === 'admin');

  } catch (err) {
    console.warn('session check failed', err);
  }
}

refreshSessionUI();