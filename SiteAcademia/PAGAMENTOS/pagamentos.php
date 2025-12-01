<?php
session_start();

// Conexão com o banco
$host = "localhost";
$user = "root";  // ajuste seu usuário
$pass = "1234";      // ajuste sua senha
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter ID do usuário da sessão
$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;

// Inserção do pagamento
$mensagem = "";
$tipo_mensagem = ""; // "sucesso" ou "erro"

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plano_input = strtoupper($_POST['plano']);
    
    // Mapear valores dos planos
    $planos = [
        'BLACK' => 149.90,
        'TECH' => 119.90,
        'FIT' => 99.90
    ];
    
    $valor = isset($planos[$plano_input]) ? $planos[$plano_input] : 0;
    
    if ($id_usuario <= 0) {
        $mensagem = "❌ Usuário não autenticado. Faça login primeiro!";
        $tipo_mensagem = "erro";
    } elseif ($valor <= 0) {
        $mensagem = "❌ Plano inválido!";
        $tipo_mensagem = "erro";
    } else {
        $status = 'Pago';
        $stmt = $conn->prepare("INSERT INTO pagamentos (id_usuario, plano, valor, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $id_usuario, $plano_input, $valor, $status);

        if ($stmt->execute()) {
            $mensagem = "✅ Pagamento registrado com sucesso!";
            $tipo_mensagem = "sucesso";
        } else {
            $mensagem = "❌ Erro ao registrar pagamento: " . $stmt->error;
            $tipo_mensagem = "erro";
        }

        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagamento | TechFit</title>
  <link rel="stylesheet" type = "text/css" href="pagamentos.css">
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
      <!-- Admin link will be added dynamically for admin users -->
      <a href="../Nossa História/nos.html">Sobre Nós</a>
      <span id="user-display" style="display:none;margin-left:12px;color:#111">Olá, <strong id="user-name"></strong></span>
    </nav>
  </header>

  <main class="fade-in-up">
    <section class="pagamento-container">
      <h1>Finalizar Pagamento</h1>
      <p>Escolha sua forma de pagamento e garanta seu plano TechFit 💪</p>

      <?php if ($mensagem): ?>
        <div class="mensagem <?php echo $tipo_mensagem; ?>" style="
          padding: 15px; 
          margin: 20px 0; 
          border-radius: 8px; 
          font-weight: bold;
          font-size: 16px;
          <?php echo ($tipo_mensagem === 'sucesso') ? 'background-color: #d4edda; color: #155724; border: 2px solid #28a745;' : 'background-color: #f8d7da; color: #721c24; border: 2px solid #f5c6cb;'; ?>
        ">
          <?php echo $mensagem; ?>
        </div>
      <?php endif; ?>

      <form class="pagamento-form" method="POST">
        <div class="form-group">
          <label for="plano">Plano selecionado:</label>
          <select id="plano" name="plano" required>
            <option value="">Selecione...</option>
            <option value="BLACK">Plano Black — R$ 149,90</option>
            <option value="TECH">Plano Tech — R$ 119,90</option>
            <option value="FIT">Plano Fit — R$ 99,90</option>
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
    // Add admin menu item only for admins
    (async function(){
      try {
        const r = await fetch('../Login/session_status.php');
        const s = await r.json();
        // populate logged-in username if present
        const uName = s.usuario || '';
        if (uName) {
          const userEl = document.getElementById('user-name');
          const userWrap = document.getElementById('user-display');
          if (userEl) userEl.textContent = uName;
          if (userWrap) userWrap.style.display = '';
        }

        if (s.perfil === 'admin') {
          const nav = document.querySelector('.nav-buttons');
          if (nav && !nav.querySelector('a[data-admin-link]')) {
            const a = document.createElement('a');
            a.href = '../Admin/painel.php';
            a.textContent = 'Painel Admin';
            a.setAttribute('data-admin-link','1');
            nav.appendChild(a);
          }
        }
      } catch(e){ console.warn('session check failed', e); }
    })();

    // Redirecionar após sucesso do pagamento
    <?php if ($tipo_mensagem === 'sucesso'): ?>
      setTimeout(function() {
        alert("✅ Pagamento confirmado com sucesso!\nBem-vindo(a) à TechFit!");
        window.location.href = "../Alunos/usuario.php";
      }, 1500);
    <?php endif; ?>
  </script>
</body>
</html>
