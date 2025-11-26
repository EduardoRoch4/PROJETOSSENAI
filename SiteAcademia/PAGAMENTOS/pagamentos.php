<?php

// Conexão com o banco
$host = "localhost";
$user = "root";  // ajuste seu usuário
$pass = "senaisp";      // ajuste sua senha
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Inserção do pagamento
$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = intval($_POST['id_usuario']); // idealmente viriam do login
    $plano      = $_POST['plano'];
    $valor      = floatval($_POST['valor']);
    $status     = 'Pago';

    $stmt = $conn->prepare("INSERT INTO pagamentos (id_usuario, plano, valor, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $id_usuario, $plano, $valor, $status);

    if ($stmt->execute()) {
        $mensagem = "✅ Pagamento registrado com sucesso!";
    } else {
        $mensagem = "❌ Erro ao registrar pagamento: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagamento | TechFit</title>
  <link rel="stylesheet" type = "text/css" href="../PAGAMENTOS/pagamentos.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <header>
    <div class="logo">
      <img src="../IMG/Logo.png" alt="Logo TechFit">
    </div>

    <nav class="nav-buttons">
      <a href="../index.html">Início</a>
      <a href="../Unidades/Unidades.html">Unidades</a>
      <a href="../Chat/chat.html">Chat</a>
      <a href="../Admin/painel.php">Painel Admin</a>
      <a href="../Nossa História/nos.html">Sobre Nós</a>
    </nav>
  </header>

  <main class="fade-in-up">
    <section class="pagamento-container">
      <h1>Finalizar Pagamento</h1>
      <p>Escolha sua forma de pagamento e garanta seu plano TechFit 💪</p>

      <form class="pagamento-form" action="#" method="POST">
        <div class="form-group">
          <label for="plano">Plano selecionado:</label>
          <select id="plano" name="plano" required>
            <option value="">Selecione...</option>
            <option value="black">Plano Black — R$ 149,90</option>
            <option value="tech">Plano Tech — R$ 119,90</option>
            <option value="fit">Plano Fit — R$ 99,90</option>
          </select>
        </div>

        <div class="form-group">
          <label for="nome">Nome completo no cartão:</label>
          <input type="text" id="nome" name="nome" placeholder="Ex: Maria da Silva" required>
        </div>

        <div class="form-group">
          <label for="numero">Número do cartão:</label>
          <input type="text" id="numero" name="numero" maxlength="19" placeholder="0000 0000 0000 0000" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="validade">Validade:</label>
            <input type="month" id="validade" name="validade" required>
          </div>

          <div class="form-group">
            <label for="cvv">CVV:</label>
            <input type="password" id="cvv" name="cvv" maxlength="4" placeholder="123" required>
          </div>
        </div>

        <div class="form-group">
          <label for="parcelas">Número de parcelas:</label>
          <select id="parcelas" name="parcelas" required>
            <option value="1x">1x de R$ 149,90</option>
            <option value="2x">2x de R$ 74,95</option>
            <option value="3x">3x de R$ 49,96</option>
          </select>
        </div>

        <button type="submit" class="btn brilho">Confirmar Pagamento</button>
      </form>
    </section>
  </main>

  <footer>
    <p>© 2025 TechFit — Todos os direitos reservados</p>
  </footer>

  <script>
    // Simula a confirmação do pagamento
    document.querySelector('.pagamento-form').addEventListener('submit', function(e) {
      e.preventDefault();
      alert("✅ Pagamento confirmado com sucesso!\nBem-vindo(a) à TechFit!");
      window.location.href = "/Alunos/usuario.php";
    });
  </script>
</body>
</html>
