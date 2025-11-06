// Redireciona se não estiver logado
if (localStorage.getItem('usuarioLogado') !== 'true') {
  window.location.href = 'login.html';
}

// Mostra nome do usuário
document.getElementById('nome-usuario').textContent = localStorage.getItem('usuarioNome') || 'Usuário TechFit';

// Logout
document.getElementById('logout').addEventListener('click', () => {
  localStorage.removeItem('usuarioLogado');
  localStorage.removeItem('usuarioNome');
  window.location.href = '/SiteAcademia/Index e Login/index.html';
});
