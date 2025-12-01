const menuIcon = document.getElementById('menu-icon');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('overlay');

menuIcon.addEventListener('click', () => {
  sideMenu.classList.add('active');
  overlay.classList.add('show');
});

closeBtn.addEventListener('click', () => {
  sideMenu.classList.remove('active');
  overlay.classList.remove('show');
});

overlay.addEventListener('click', () => {
  sideMenu.classList.remove('active');
  overlay.classList.remove('show');
});

// --- ANIMAÇÕES DE ROLAGEM ---
const fadeElements = document.querySelectorAll('.fade-in-up');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('visible');
  });
}, { threshold: 0.2 });
fadeElements.forEach(el => observer.observe(el));

// --- CONTROLE DE LOGIN/SESSION ---
const loginBtn = document.getElementById('login-btn');
const perfilBtn = document.getElementById('perfil-btn');
const loginSide = document.getElementById('login-side');
const perfilSide = document.getElementById('perfil-side');

// --- BOTÃO ÚNICO DE LOGOUT ---
const logoutBtn = document.getElementById("logout-btn");

// 🔥 FUNÇÃO GLOBAL DE LOGOUT
function fazerLogout(e) {
  if (e) e.preventDefault();
  window.location.href = "Login/login.php?acao=logout";
}

// Aplicar logout ao botão único
if (logoutBtn) logoutBtn.onclick = fazerLogout;


// 🔥 ATUALIZA INTERFACE BASEADA NA SESSÃO
async function refreshSessionUI() {
  try {
    const r = await fetch('Login/session_status.php');
    const s = await r.json();
    const isLogged = !!s.logged;
    const perfil = s.perfil || null;

    const userNameEl = document.getElementById('user-name');
    const userDisplay = document.getElementById('user-display');
    const userNameSide = document.getElementById('user-name-side');
    const userDisplaySide = document.getElementById('user-display-side');

    if (isLogged) {

      // LOGIN VIRA LOGOUT
      loginBtn.textContent = 'Logout';
      loginSide.textContent = 'Logout';

      // Torna os botões de login em logout
      loginBtn.onclick = fazerLogout;
      loginSide.onclick = fazerLogout;
      loginBtn.removeAttribute("href");
      loginSide.removeAttribute("href");

      // MOSTRAR PERFIL APENAS SE O USUÁRIO NÃO FOR ADMIN
      if (perfil === 'admin') {
        perfilBtn.style.display = 'inline-block';
        perfilSide.style.display = 'inline-block';
      } else {
        perfilBtn.style.display = 'none';
        perfilSide.style.display = 'none';
      }

      // MOSTRAR BOTÃO DE LOGOUT ÚNICO
      if (logoutBtn) logoutBtn.style.display = "inline-block";

      // MOSTRAR NOME
      if (userNameEl) userNameEl.textContent = s.usuario || '';
      if (userDisplay) userDisplay.style.display = '';
      if (userNameSide) userNameSide.textContent = s.usuario || '';
      if (userDisplaySide) userDisplaySide.style.display = '';

    } else {

      // LOGIN NORMAL
      loginBtn.textContent = 'Login';
      loginSide.textContent = 'Login';
      loginBtn.href = 'Login/login.php';
      loginSide.href = 'Login/login.php';

      // REMOVER EVENTOS DO LOGOUT
      loginBtn.onclick = null;
      loginSide.onclick = null;

      // ESCONDER PERFIL E ADMIN
      perfilBtn.style.display = 'none';
      perfilSide.style.display = 'none';

      // ESCONDER LOGOUT ÚNICO
      if (logoutBtn) logoutBtn.style.display = "none";

      // LIMPAR NOME
      if (userNameEl) userNameEl.textContent = '';
      if (userDisplay) userDisplay.style.display = 'none';
      if (userNameSide) userNameSide.textContent = '';
      if (userDisplaySide) userDisplaySide.style.display = '';
    }

    // 🔥 MOSTRA/REMOVE LINK DO ADMIN SÓ SE O PERFIL FOR ADMIN
    function setAdminLinks(show) {
      const nav = document.querySelector('.nav-buttons') || document.getElementById('nav-buttons');
      if (nav) {
        let a = nav.querySelector('a[data-admin-link]');
        if (show && !a) {
          a = document.createElement('a');
          a.href = '/Admin/painel.php';
          a.textContent = 'Painel Admin';
          a.dataset.adminLink = '1';
          nav.appendChild(a);
        }
        if (!show && a) a.remove();
      }

      const side = document.getElementById('side-menu');
      if (side) {
        let s = side.querySelector('a[data-admin-link-side]');
        if (show && !s) {
          s = document.createElement('a');
          s.href = '/Admin/painel.php';
          s.textContent = 'Painel Admin';
          s.dataset.adminLinkSide = '1';
          side.appendChild(s);
        }
        if (!show && s) s.remove();
      }
    }

    setAdminLinks(perfil === 'admin');

  } catch (e) {
    console.warn('Could not check session status:', e);
  }
}

refreshSessionUI();
