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

    // --- CONTROLE DE LOGIN ---
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
        loginBtn.href = 'login.html';
        loginSide.href = 'login.html';
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