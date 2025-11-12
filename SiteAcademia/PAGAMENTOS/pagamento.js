function getParametro(nome) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(nome);
}

// Atualiza o valor do plano automaticamente
function atualizarPreco(plano) {
  const precoSpan = document.getElementById("preco");
  let preco = "R$ 0,00";

  switch (plano.toLowerCase()) {
    case "black":
      preco = "R$ 149,90";
      break;
    case "fit":
      preco = "R$ 99,90";
      break;
    case "tech":
      preco = "R$ 119,90";
      break;
  }

  precoSpan.textContent = preco;
}

// Quando a página carregar...
document.addEventListener("DOMContentLoaded", () => {
  const planoSelecionado = getParametro("plano");
  const selectPlano = document.getElementById("plano");

  // Se veio o plano da página anterior, já seleciona
  if (planoSelecionado && selectPlano) {
    selectPlano.value = planoSelecionado.toLowerCase();
    atualizarPreco(planoSelecionado);
  }

  // Atualiza o preço ao mudar o plano no select
  selectPlano?.addEventListener("change", (e) => {
    atualizarPreco(e.target.value);
  });
});

// Validação e simulação do pagamento
document.querySelector(".pagamento-form")?.addEventListener("submit", (e) => {
  e.preventDefault();

  const nome = document.getElementById("nome").value.trim();
  const email = document.getElementById("email").value.trim();
  const cartao = document.getElementById("cartao").value.trim();
  const plano = document.getElementById("plano").value;
  const preco = document.getElementById("preco").textContent;

  if (!nome || !email || !cartao || !plano) {
    alert("⚠️ Por favor, preencha todos os campos obrigatórios.");
    return;
  }

  // Simula o processamento
  alert(`✅ Pagamento aprovado!
  
Plano: ${plano.toUpperCase()}
Valor: ${preco}
Cliente: ${nome}

Bem-vindo(a) à TechFit! 💪`);

  // Redireciona após confirmar
  window.location.href = "/SiteAcademia/Usuario/usuario.html";
});