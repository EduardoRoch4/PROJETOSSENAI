// 🧩 Modal de conteúdo
const modal = document.getElementById('modal');
const close = document.getElementById('close');
const titulo = document.getElementById('modal-titulo');
const texto = document.getElementById('modal-texto');

function abrirModal(tipo) {
  modal.style.display = 'flex';
  if (tipo === 'alunos') {
    titulo.textContent = 'Gerenciamento de Alunos';
    texto.textContent = 'Aqui você poderá visualizar, cadastrar e editar informações dos alunos (em desenvolvimento).';
  } else if (tipo === 'professores') {
    titulo.textContent = 'Gerenciamento de Professores';
    texto.textContent = 'Controle de dados de instrutores e turmas (em breve).';
  } else if (tipo === 'relatorios') {
    titulo.textContent = 'Relatórios Gerenciais';
    texto.textContent = 'Visualize relatórios de presença, ocupação e desempenho (em breve).';
  }
}

close.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', (e) => {
  if (e.target === modal) modal.style.display = 'none';
});

// 🟢 Menu lateral e overlay (igual ao index)
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

// 🟢 Controle de login/logout
const loginBtn = document.getElementById('login-btn');
const perfilBtn = document.getElementById('perfil-btn');
const loginSide = document.getElementById('login-side');
const perfilSide = document.getElementById('perfil-side');

function atualizarInterface() {
  const logado = localStorage.getItem('usuarioLogado') === 'true';
  if (logado) {
    loginBtn.textContent = 'Logout';
    loginSide.textContent = 'Logout';
    loginBtn.href = '#';
    loginSide.href = '#';
    perfilBtn.style.display = 'inline-block';
    perfilSide.style.display = 'inline-block';
  } else {
    loginBtn.textContent = 'Login';
    loginSide.textContent = 'Login';
    loginBtn.href = '../login.php';
    loginSide.href = '../login.php';
    perfilBtn.style.display = 'none';
    perfilSide.style.display = 'none';
  }
}

loginBtn.addEventListener('click', () => {
  if (loginBtn.textContent === 'Logout') {
    localStorage.removeItem('usuarioLogado');
    atualizarInterface();
  }
});

loginSide.addEventListener('click', () => {
  if (loginSide.textContent === 'Logout') {
    localStorage.removeItem('usuarioLogado');
    atualizarInterface();
    sideMenu.classList.remove('active');
    overlay.classList.remove('show');
  }
});

atualizarInterface();
