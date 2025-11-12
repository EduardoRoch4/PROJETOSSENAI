<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.php"); // Redireciona para login se não estiver logado
    exit;
}

// Aqui você poderia buscar mais informações do banco se quiser dados detalhados
$nomeUsuario = $_SESSION['usuario'];
$emailUsuario = "user@techfit.com"; // Exemplo estático, substitua com dados reais do banco
$matricula = "#000123";
$telefone = "(11) 99999-9999";
$unidade = "TechFit Paulista";
$proximoPagamento = "10/11/2025";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil | TechFit</title>
  <link rel="stylesheet" href="usuario.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="logo">
      <img src="../IMG/Logo.png" alt="Logo TechFit">
    </div>

    <nav class="nav-buttons">
      <a href="../Agendamento/agendamento.html">Agendamento</a>
      <a href="../Unidades/Unidades.html">Unidades</a>
      <a href="/SiteAcademia/Nossa História/nos.html">Sobre Nós</a>
      <a href="../Login/logout.php" id="logout">Logout</a>
    </nav>
  </header>

  <main class="profile-container fade-in-up">
    <div class="profile-card">
      <img src="../IMG/avatar-placeholder.png" alt="Foto do Usuário" class="profile-pic">
      <h2 id="nome-usuario"><?php echo htmlspecialchars($nomeUsuario); ?></h2>
      <h3 class="plano-ativo">Plano Black</h3>

      <div class="profile-info">
        <div class="info-row"><strong>Matrícula:</strong> <span><?php echo htmlspecialchars($matricula); ?></span></div>
        <div class="info-row"><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($emailUsuario); ?></span></div>
        <div class="info-row"><strong>Telefone:</strong> <span><?php echo htmlspecialchars($telefone); ?></span></div>
        <div class="info-row"><strong>Unidade:</strong> <span><?php echo htmlspecialchars($unidade); ?></span></div>
        <div class="info-row"><strong>Próximo Pagamento:</strong> <span><?php echo htmlspecialchars($proximoPagamento); ?></span></div>
      </div>

      <div class="profile-actions">
        <a href="#" class="btn">Editar Perfil</a>
        <a href="../Agendamento/agendamento.html" class="btn">Agendamentos</a>
        <a href="#" class="btn">Alterar Senha</a>
        <a href="../index.html" class="btn voltar">Voltar ao Início</a>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 TechFit — Todos os direitos reservados</p>
  </footer>

  <script src="usuario.js"></script>
</body>
</html>
