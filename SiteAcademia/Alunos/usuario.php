<?php
session_start(); // Inicia a sessão PHP

// 1. AÇÃO DE LOGOUT
// Verifica se o usuário clicou no link de logout
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
    session_unset();     // Limpa todas as variáveis da sessão
    session_destroy();   // Destrói a sessão
    
    // Redireciona para o login (caminho baseado no seu usuario.js)
    header("Location: ../Index e Login/login.php"); 
    exit;
}

// 2. VERIFICAÇÃO DE LOGIN
// Se não houver uma sessão ativa, manda o usuário para a página de login
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['usuario'])) {
    header("Location: ../Index e Login/login.php");
    exit;
}

// 3. CONEXÃO COM O BANCO DE DADOS
// (Mesma conexão do seu login.php)
$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

// 4. BUSCAR DADOS DO USUÁRIO PARA A PÁGINA
$id_usuario = $_SESSION['id_usuario'];
$nome_usuario = $_SESSION['usuario']; // Nome já veio da sessão

// Inicializa variáveis com valores padrão
$dados_usuario = ['email' => 'Email não cadastrado'];
$dados_pagamento = ['plano' => 'Nenhum', 'data_pagamento' => null];
$proximo_pag = 'N/A';

// Busca o email do usuário no banco
$stmt_user = $conn->prepare("SELECT email FROM usuarios WHERE id_usuario = ?");
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$resultado_user = $stmt_user->get_result();
if ($resultado_user->num_rows > 0) {
    $dados_usuario = $resultado_user->fetch_assoc();
}
$stmt_user->close();

// Busca o último pagamento (plano e data)
$stmt_pag = $conn->prepare("SELECT plano, data_pagamento FROM pagamentos WHERE id_usuario = ? ORDER BY data_pagamento DESC LIMIT 1");
$stmt_pag->bind_param("i", $id_usuario);
$stmt_pag->execute();
$resultado_pag = $stmt_pag->get_result();
if ($resultado_pag->num_rows > 0) {
    $dados_pagamento = $resultado_pag->fetch_assoc();

    // Calcula a data do próximo pagamento (data do último + 1 mês)
    if ($dados_pagamento['data_pagamento']) {
        try {
            $data_pag = new DateTime($dados_pagamento['data_pagamento']);
            $data_pag->modify('+1 month');
            $proximo_pag = $data_pag->format('d/m/Y');
        } catch (Exception $e) {
            $proximo_pag = 'Erro ao calcular';
        }
    }
}
$stmt_pag->close();
$conn->close();

// Formata o ID de usuário como matrícula
$matricula = '#' . str_pad($id_usuario, 6, '0', STR_PAD_LEFT);

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
      
      <a href="usuario.php?acao=logout" id="logout">Logout</a>
    </nav>
  </header>

  <main class="profile-container fade-in-up">
    <div class="profile-card">
      <img src="../IMG/avatar-placeholder.png" alt="Foto do Usuário" class="profile-pic">
      
      <h2 id="nome-usuario"><?php echo htmlspecialchars($nome_usuario); ?></h2>
      <h3 class="plano-ativo">Plano <?php echo htmlspecialchars(strtoupper($dados_pagamento['plano'])); ?></h3>

      <div class="profile-info">
        <div class="info-row"><strong>Matrícula:</strong> <span><?php echo $matricula; ?></span></div>
        <div class="info-row"><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($dados_usuario['email']); ?></span></div>
        
        <div class="info-row"><strong>Telefone:</strong> <span>(11) 99999-9999</span></div>
        <div class="info-row"><strong>Unidade:</strong> <span>TechFit Paulista</span></div>
        
        <div class="info-row"><strong>Próximo Pagamento:</strong> <span><?php echo $proximo_pag; ?></span></div>
      </div>
      <div class="profile-actions">
        <a href="#" class="btn">Editar Perfil</a>
        <a href="../Agendamento/agendamento.html" class="btn">Agendamentos</a>
        <a href="#" class="btn">Alterar Senha</a>
        <a href="/SiteAcademia/Index e Login/index.html" class="btn voltar">Voltar ao Início</a>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 TechFit — Todos os direitos reservados</p>
  </footer>

  </body>
</html>