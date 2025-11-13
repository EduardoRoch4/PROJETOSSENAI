// 🟢 Função de chat
const input = document.getElementById('mensagem');
const enviar = document.getElementById('enviar');
const chatBox = document.getElementById('chat-box');
// 🟢 ADICIONADO: Seleciona o container dos botões
const quickRepliesContainer = document.getElementById('quick-replies');

function adicionarMensagem(texto, tipo = 'user') {
  const msg = document.createElement('div');
  msg.classList.add('message', tipo);
  msg.textContent = texto;
  chatBox.appendChild(msg);
  chatBox.scrollTop = chatBox.scrollHeight;
}

// 🟢 ADICIONADO: Banco de dados de perguntas e respostas
const respostasProntas = {
  "Quais são os horários de funcionamento?": "Nossa academia funciona de Seg. a Sex. das 6h às 23h, e Sáb. das 8h às 14h.",
  "Quais planos vocês oferecem?": "Temos planos Mensal, Trimestral e Anual. O plano anual é o nosso melhor custo-benefício! Para mais detalhes, acesse nossa página de planos.",
  "Como agendar uma aula?": "Você pode agendar aulas de funcional, spinning ou yoga diretamente pela nossa página de 'Agendamento' no menu."
};

// 🟢 ADICIONADO: Função para buscar resposta
function obterResposta(pergunta) {
  // Verifica se a pergunta existe no nosso banco de respostas
  if (respostasProntas[pergunta]) {
    return respostasProntas[pergunta];
  }
  // Resposta padrão para perguntas personalizadas
  return 'Entendido! Um instrutor entrará em contato em breve.';
}

// 🟢 MODIFICADO: Evento de clique do botão Enviar
enviar.addEventListener('click', () => {
  const texto = input.value.trim();
  if (texto !== '') {
    adicionarMensagem(texto, 'user');
    input.value = '';

    // Busca a resposta correta (pronta ou padrão)
    const resposta = obterResposta(texto);

    setTimeout(() => {
      adicionarMensagem(resposta, 'system');
    }, 800);
  }
});

input.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') enviar.click();
});

// 🟢 ADICIONADO: Evento de clique para os botões de perguntas rápidas
quickRepliesContainer.addEventListener('click', (e) => {
  // Verifica se o clique foi em um botão com a classe 'quick-reply'
  if (e.target.classList.contains('quick-reply')) {
    // Pega a pergunta completa do atributo 'data-question'
    const pergunta = e.target.dataset.question;
    
    // 1. Adiciona a pergunta do usuário ao chat
    adicionarMensagem(pergunta, 'user');
    
    // 2. Obtém a resposta correspondente
    const resposta = obterResposta(pergunta); // Com certeza vai achar a resposta
    
    // 3. Adiciona a resposta do sistema ao chat
    setTimeout(() => {
      adicionarMensagem(resposta, 'system');
    }, 800);
  }
});


// 🟢 Menu lateral e overlay (SEU CÓDIGO ORIGINAL - SEM MUDANÇAS)
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

// 🟢 Controle de login/logout (SEU CÓDIGO ORIGINAL - SEM MUDANÇAS)
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

// 🟢 Animação (SEU CÓDIGO ORIGINAL - SEM MUDANÇAS)
const fadeElements = document.querySelectorAll('.fade-in-up');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.2 });
    fadeElements.forEach(el => observer.observe(el));

// 🟢 CORREÇÃO: A chave '}' extra que estava aqui no seu arquivo original foi removida.