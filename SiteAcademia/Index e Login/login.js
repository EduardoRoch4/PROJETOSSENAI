const formLogin = document.getElementById('form-login');
    const formCadastro = document.getElementById('form-cadastro');
    const mostrarCadastro = document.getElementById('mostrar-cadastro');
    const mostrarLogin = document.getElementById('mostrar-login');
    const msg = document.getElementById('mensagem');

    mostrarCadastro.addEventListener('click', (e) => {
      e.preventDefault();
      formLogin.style.display = 'none';
      formCadastro.style.display = 'block';
      msg.textContent = '';
    });

    mostrarLogin.addEventListener('click', (e) => {
      e.preventDefault();
      formLogin.style.display = 'block';
      formCadastro.style.display = 'none';
      msg.textContent = '';
    });

    document.getElementById('btn-cadastrar').addEventListener('click', () => {
      const usuario = document.getElementById('cadastro-usuario').value;
      const senha = document.getElementById('cadastro-senha').value;
      if (usuario && senha) {
        localStorage.setItem('usuario', usuario);
        localStorage.setItem('senha', senha);
        msg.textContent = '✅ Cadastro realizado com sucesso!';
        msg.style.color = 'green';
        setTimeout(() => {
          formCadastro.style.display = 'none';
          formLogin.style.display = 'block';
          msg.textContent = '';
        }, 1500);
      } else {
        msg.textContent = 'Preencha todos os campos.';
        msg.style.color = 'red';
      }
    });

    document.getElementById('btn-login').addEventListener('click', () => {
      const usuario = document.getElementById('login-usuario').value;
      const senha = document.getElementById('login-senha').value;
      const userCadastrado = localStorage.getItem('usuario');
      const senhaCadastrada = localStorage.getItem('senha');
      if (usuario === userCadastrado && senha === senhaCadastrada) {
        localStorage.setItem('usuarioLogado', 'true');
        localStorage.setItem('usuarioNome', usuario);
        window.location.href = '/SiteAcademia/Index e Login/index.html';
      } else {
        msg.textContent = 'Usuário ou senha incorretos.';
        msg.style.color = 'red';
      }
    });

    document.getElementById('btn-voltar').addEventListener('click', () => window.location.href = 'index.html');
    document.getElementById('btn-voltar2').addEventListener('click', () => window.location.href = 'index.html');