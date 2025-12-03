// 🧩 Modal de conteúdo
const modal = document.getElementById('modal');
const close = document.getElementById('close');
const titulo = document.getElementById('modal-titulo');

function abrirModal() {
  if (modal) modal.style.display = 'flex';
}

if (close) {
  close.addEventListener('click', () => {
    if (modal) modal.style.display = 'none';
  });
}

window.addEventListener('click', (e) => {
  if (e.target === modal) {
    if (modal) modal.style.display = 'none';
  }
});

// 🟢 Menu lateral e overlay
const menuIcon = document.getElementById('menu-icon');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('overlay');

if (menuIcon) {
  menuIcon.addEventListener('click', () => {
    if (sideMenu) sideMenu.classList.add('active');
    if (overlay) overlay.classList.add('show');
  });
}

if (closeBtn) {
  closeBtn.addEventListener('click', () => {
    if (sideMenu) sideMenu.classList.remove('active');
    if (overlay) overlay.classList.remove('show');
  });
}

if (overlay) {
  overlay.addEventListener('click', () => {
    if (sideMenu) sideMenu.classList.remove('active');
    if (overlay) overlay.classList.remove('show');
  });
}

// 🟢 Controle de login/logout
const loginBtn = document.getElementById('login-btn');
const perfilBtn = document.getElementById('perfil-btn');

async function atualizarInterface() {
  try {
    const r = await fetch('../Login/session_status.php');
    const s = await r.json();
    const isLogged = !!s.logged;
    const perfil = s.perfil || null;

    if (isLogged) {
      if (loginBtn) {
        loginBtn.textContent = 'Logout';
        loginBtn.href = '/Login/login.php?acao=logout';
      }
      if (perfilBtn) {
        perfilBtn.style.display = 'inline-block';
      }
      const userNameEl = document.getElementById('user-name');
      const userDisplay = document.getElementById('user-display');
      if (userNameEl) userNameEl.textContent = s.usuario || '';
      if (userDisplay) userDisplay.style.display = isLogged ? '' : 'none';
    } else {
      if (loginBtn) {
        loginBtn.textContent = 'Login';
        loginBtn.href = '/Login/login.php';
      }
      if (perfilBtn) {
        perfilBtn.style.display = 'none';
      }
    }

    // hide non-admin features for non-admins
    const adminLinks = document.querySelectorAll('a[href*="/Admin/painel.php"], a[href*="Admin/admin.html"], a[href*="/Admin/"]');
    adminLinks.forEach(a => a.style.display = (perfil === 'admin') ? '' : 'none');

  } catch (err) {
    console.warn('session check error', err);
  }
}

atualizarInterface();

const fadeElements = document.querySelectorAll('.fade-in-up');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('visible');
  });
}, { threshold: 0.2 });
fadeElements.forEach(el => observer.observe(el));

// ===== FUNCIONALIDADES POR PÁGINA =====

// 🎓 PÁGINA DE ALUNOS
const formAluno = document.getElementById('form-aluno');
const novoAlunoBtn = document.getElementById('novo-aluno');
const searchInput = document.getElementById('search-input');

if (novoAlunoBtn) {
  novoAlunoBtn.addEventListener('click', () => {
    document.getElementById('aluno-id').value = '';
    document.getElementById('aluno-nome').value = '';
    document.getElementById('aluno-email').value = '';
    abrirModal();
  });
}

if (formAluno) {
  formAluno.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('aluno-id').value;
    const nome = document.getElementById('aluno-nome').value;
    const email = document.getElementById('aluno-email').value;

    const dados = { nome, email };
    if (id) dados.id = id;

    try {
      const res = await fetch('api_alunos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
      });
      const result = await res.json();
      if (result.status === 'ok') {
        alert('Aluno salvo com sucesso!');
        location.reload();
      } else {
        alert('Erro: ' + (result.message || 'Erro desconhecido'));
      }
    } catch (err) {
      console.error('Erro:', err);
      alert('Erro ao salvar aluno');
    }
  });
}

// Editar aluno
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('editar') && window.location.pathname.includes('alunos.php')) {
    const id = e.target.getAttribute('data-id');
    const tr = e.target.closest('tr');
    const nome = tr.cells[1].textContent;
    const email = tr.cells[2].textContent;

    document.getElementById('aluno-id').value = id;
    document.getElementById('aluno-nome').value = nome;
    document.getElementById('aluno-email').value = email;
    abrirModal();
  }

  // Deletar aluno
  if (e.target.classList.contains('deletar') && window.location.pathname.includes('alunos.php')) {
    const id = e.target.getAttribute('data-id');
    if (confirm('Tem certeza que deseja deletar este aluno?')) {
      fetch('api_alunos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, action: 'delete' })
      })
      .then(async res => {
        // Verificar status HTTP
        if (!res.ok) {
          console.error('Status HTTP:', res.status, res.statusText);
          const text = await res.text();
          console.error('Resposta do servidor:', text);
          throw new Error('Erro HTTP ' + res.status + ': ' + res.statusText);
        }
        
        // Verificar Content-Type
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          console.warn('Content-Type inesperado:', contentType);
        }
        
        const responseText = await res.text();
        console.log('Resposta da API (raw):', responseText);
        
        // Verificar se a resposta é JSON válido
        try {
          return JSON.parse(responseText);
        } catch (parseError) {
          console.error('Erro ao fazer parse do JSON:', parseError);
          console.error('Resposta completa recebida:', responseText);
          throw new Error('Resposta inválida do servidor');
        }
      })
      .then(result => {
        if (result.status === 'ok') {
          alert('Aluno deletado com sucesso!');
          location.reload();
        } else {
          alert('Erro ao deletar: ' + (result.message || 'Erro desconhecido'));
          console.error('Erro na API:', result);
        }
      })
      .catch(err => {
        console.error('Erro ao conectar com API:', err);
        alert('Erro ao deletar aluno: ' + err.message);
      });
    }
  }
});

// 👨‍🏫 PÁGINA DE PROFESSORES
const formProfessor = document.getElementById('form-professor');
const novoProfessorBtn = document.getElementById('novo-professor');

if (novoProfessorBtn) {
  novoProfessorBtn.addEventListener('click', () => {
    document.getElementById('professor-id').value = '';
    document.getElementById('professor-nome').value = '';
    document.getElementById('professor-email').value = '';
    document.getElementById('professor-especialidade').value = '';
    abrirModal();
  });
}

if (formProfessor) {
  formProfessor.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('professor-id').value;
    const nome = document.getElementById('professor-nome').value;
    const email = document.getElementById('professor-email').value;
    const especialidade = document.getElementById('professor-especialidade').value;

    const dados = { nome, email, especialidade };
    if (id) dados.id = id;

    try {
      const res = await fetch('api_professores.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
      });
      const result = await res.json();
      if (result.status === 'ok') {
        alert('Professor salvo com sucesso!');
        location.reload();
      } else {
        alert('Erro: ' + (result.message || 'Erro desconhecido'));
      }
    } catch (err) {
      console.error('Erro:', err);
      alert('Erro ao salvar professor');
    }
  });
}

// Editar/Deletar professor
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('editar') && window.location.pathname.includes('professores.php')) {
    const id = e.target.getAttribute('data-id');
    const tr = e.target.closest('tr');
    const nome = tr.cells[1].textContent;
    const email = tr.cells[2].textContent;
    const especialidade = tr.cells[3].textContent;

    document.getElementById('professor-id').value = id;
    document.getElementById('professor-nome').value = nome;
    document.getElementById('professor-email').value = email;
    document.getElementById('professor-especialidade').value = especialidade === '—' ? '' : especialidade;
    abrirModal();
  }

  if (e.target.classList.contains('deletar') && window.location.pathname.includes('professores.php')) {
    const id = e.target.getAttribute('data-id');
    if (confirm('Tem certeza que deseja deletar este professor?')) {
      fetch('api_professores.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, action: 'delete' })
      })
      .then(res => res.json())
      .then(result => {
        if (result.status === 'ok') {
          alert('Professor deletado!');
          location.reload();
        } else {
          alert('Erro ao deletar: ' + result.message);
        }
      })
      .catch(err => console.error('Erro:', err));
    }
  }
});

// 📚 PÁGINA DE AULAS
const formAula = document.getElementById('form-aula');
const novaAulaBtn = document.getElementById('nova-aula');

if (novaAulaBtn) {
  novaAulaBtn.addEventListener('click', () => {
    document.getElementById('aula-id').value = '';
    document.getElementById('aula-nome').value = '';
    document.getElementById('aula-descricao').value = '';
    document.getElementById('aula-horario').value = '';
    document.getElementById('aula-professor').value = '';
    abrirModal();
  });
}

if (formAula) {
  formAula.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('aula-id').value;
    const nome = document.getElementById('aula-nome').value;
    const descricao = document.getElementById('aula-descricao').value;
    const horario = document.getElementById('aula-horario').value;
    const professor = document.getElementById('aula-professor').value;

    const dados = { nome, descricao, horario, professor };
    if (id) dados.id = id;

    try {
      const res = await fetch('api_aulas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
      });
      const result = await res.json();
      if (result.status === 'ok') {
        alert('Aula salva com sucesso!');
        location.reload();
      } else {
        alert('Erro: ' + (result.message || 'Erro desconhecido'));
      }
    } catch (err) {
      console.error('Erro:', err);
      alert('Erro ao salvar aula');
    }
  });
}

// Editar/Deletar aula
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('editar') && window.location.pathname.includes('aulas.php')) {
    const id = e.target.getAttribute('data-id');
    const tr = e.target.closest('tr');
    const nome = tr.cells[1].textContent;
    const descricao = tr.cells[2].textContent;
    const horario = tr.cells[3].textContent;

    document.getElementById('aula-id').value = id;
    document.getElementById('aula-nome').value = nome;
    document.getElementById('aula-descricao').value = descricao === '—' ? '' : descricao;
    document.getElementById('aula-horario').value = horario === '—' ? '' : horario;
    abrirModal();
  }

  if (e.target.classList.contains('deletar') && window.location.pathname.includes('aulas.php')) {
    const id = e.target.getAttribute('data-id');
    if (confirm('Tem certeza que deseja deletar esta aula?')) {
      fetch('api_aulas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, action: 'delete' })
      })
      .then(res => res.json())
      .then(result => {
        if (result.status === 'ok') {
          alert('Aula deletada!');
          location.reload();
        } else {
          alert('Erro ao deletar: ' + result.message);
        }
      })
      .catch(err => console.error('Erro:', err));
    }
  }
});

// 📅 PÁGINA DE AGENDAMENTOS
const formAgendamento = document.getElementById('form-agendamento');
const novoAgendamentoBtn = document.getElementById('novo-agendamento');

if (novoAgendamentoBtn) {
  novoAgendamentoBtn.addEventListener('click', () => {
    document.getElementById('agendamento-id').value = '';
    document.getElementById('agendamento-usuario').value = '';
    document.getElementById('agendamento-data').value = '';
    document.getElementById('agendamento-objetivo').value = '';
    document.getElementById('agendamento-modalidade').value = '';
    document.getElementById('agendamento-status').value = '';
    abrirModal();
  });
}

if (formAgendamento) {
  formAgendamento.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('agendamento-id').value;
    const usuario = document.getElementById('agendamento-usuario').value;
    const data = document.getElementById('agendamento-data').value;
    const objetivo = document.getElementById('agendamento-objetivo').value;
    const modalidade = document.getElementById('agendamento-modalidade').value;
    const status = document.getElementById('agendamento-status').value;

    // Validar campos obrigatórios
    if (!usuario) {
      alert('Por favor, selecione um usuário');
      return;
    }
    if (!data) {
      alert('Por favor, selecione uma data e hora');
      return;
    }
    if (!objetivo) {
      alert('Por favor, informe o objetivo');
      return;
    }
    if (!status) {
      alert('Por favor, selecione um status');
      return;
    }

    console.log('Enviando dados:', { usuario, data, objetivo, modalidade, status });

    const dados = { usuario, data, objetivo, modalidade, status };
    if (id) dados.id = id;

    try {
      const res = await fetch('api_agendamentos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
      });
      
      // Verificar status HTTP
      if (!res.ok) {
        console.error('Status HTTP:', res.status, res.statusText);
        const text = await res.text();
        console.error('Resposta do servidor:', text);
        alert('Erro HTTP ' + res.status + ': ' + res.statusText);
        return;
      }
      
      // Verificar Content-Type
      const contentType = res.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        console.warn('Content-Type inesperado:', contentType);
      }
      
      const responseText = await res.text();
      console.log('Resposta da API (raw):', responseText);
      console.log('Primeiros 200 caracteres:', responseText.substring(0, 200));
      
      // Verificar se a resposta é JSON válido
      let result;
      try {
        result = JSON.parse(responseText);
      } catch (parseError) {
        console.error('Erro ao fazer parse do JSON:', parseError);
        console.error('Resposta completa recebida:', responseText);
        console.error('Tipo da resposta:', typeof responseText);
        console.error('Tamanho da resposta:', responseText.length);
        
        // Tentar identificar o problema
        if (responseText.trim().startsWith('<')) {
          alert('Erro: O servidor retornou HTML em vez de JSON. Isso geralmente indica um erro PHP. Verifique o console para mais detalhes.');
        } else {
          alert('Erro: O servidor retornou uma resposta inválida. Verifique o console para mais detalhes.');
        }
        return;
      }
      
      if (result.status === 'ok') {
        alert('Agendamento salvo com sucesso!');
        location.reload();
      } else {
        alert('Erro: ' + (result.message || 'Erro desconhecido'));
        console.error('Erro completo:', result);
      }
    } catch (err) {
      console.error('Erro ao processar:', err);
      alert('Erro ao salvar agendamento: ' + err.message);
    }
  });
}

// Editar/Deletar agendamento
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('editar') && window.location.pathname.includes('agendamentos.php')) {
    const id = e.target.getAttribute('data-id');
    const tr = e.target.closest('tr');
    
    // Obter dados dos atributos data da linha
    const usuarioId = tr.getAttribute('data-usuario-id');
    const dataHora = tr.getAttribute('data-data-hora');
    const objetivo = tr.getAttribute('data-objetivo');
    const modalidade = tr.getAttribute('data-modalidade');
    const status = tr.getAttribute('data-status');

    document.getElementById('agendamento-id').value = id;
    document.getElementById('agendamento-usuario').value = usuarioId || '';
    document.getElementById('agendamento-data').value = dataHora || '';
    document.getElementById('agendamento-objetivo').value = objetivo || '';
    document.getElementById('agendamento-modalidade').value = modalidade || '';
    document.getElementById('agendamento-status').value = status || '';
    abrirModal();
  }

  if (e.target.classList.contains('deletar') && window.location.pathname.includes('agendamentos.php')) {
    const id = e.target.getAttribute('data-id');
    if (confirm('Tem certeza que deseja deletar este agendamento?')) {
      fetch('api_agendamentos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, action: 'delete' })
      })
      .then(res => res.json())
      .then(result => {
        if (result.status === 'ok') {
          alert('Agendamento deletado!');
          location.reload();
        } else {
          alert('Erro ao deletar: ' + result.message);
        }
      })
      .catch(err => console.error('Erro:', err));
    }
  }
});

// 🔍 Busca
if (searchInput) {
  searchInput.addEventListener('input', (e) => {
    const termo = e.target.value.toLowerCase();
    const tabelas = document.querySelectorAll('table tbody tr');
    tabelas.forEach(tr => {
      const texto = tr.textContent.toLowerCase();
      tr.style.display = texto.includes(termo) ? '' : 'none';
    });
  });
}
