<?php
session_start();

// ====== CONEXÃO COM O BANCO ======
$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$mensagem = "";

// ====== CADASTRO DE USUÁRIO ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] === 'cadastro') {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);

    if (!empty($usuario) && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'aluno')");
        $stmt->bind_param("sss", $usuario, $usuario, $senhaHash);

        if ($stmt->execute()) {
            $mensagem = "✅ Usuário cadastrado com sucesso!";
        } else {
            if ($conn->errno == 1062) {
                $mensagem = "⚠️ Usuário já existe.";
            } else {
                $mensagem = "❌ Erro ao cadastrar: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $mensagem = "⚠️ Preencha todos os campos!";
    }
}

// ====== LOGIN DE USUÁRIO ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] === 'login') {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);

    if (!empty($usuario) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id_usuario, nome, senha FROM usuarios WHERE nome = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $dados = $resultado->fetch_assoc();

            if (password_verify($senha, $dados['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario'] = $dados['nome'];
                $_SESSION['id_usuario'] = $dados['id_usuario'];
                header("/SiteAcademia/Alunos/usuario.php");
                exit;
            } else {
                $mensagem = "❌ Senha incorreta!";
            }
        } else {
            $mensagem = "⚠️ Usuário não encontrado!";
        }
        $stmt->close();
    } else {
        $mensagem = "⚠️ Preencha todos os campos!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TechFit</title>
  <link rel="stylesheet" href="login.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h1>TechFit</h1>

      <!-- Mensagem PHP -->
      <?php if(!empty($mensagem)) { echo "<p id='mensagem'>$mensagem</p>"; } ?>

      <!-- ===== FORM LOGIN ===== -->
      <div id="form-login">
        <form method="POST" action="">
          <input type="hidden" name="tipo" value="login">
          <input type="text" name="usuario" id="login-usuario" placeholder="Usuário" required>
          <input type="password" name="senha" id="login-senha" placeholder="Senha" required>
          <button type="submit" id="btn-login">Entrar</button>
        </form>
        <button id="btn-voltar" class="btn-voltar">⬅ Voltar</button>
        <p>Não tem conta? <a href="#" id="mostrar-cadastro">Cadastre-se</a></p>
      </div>

      <!-- ===== FORM CADASTRO ===== -->
      <div id="form-cadastro" style="display:none;">
        <form method="POST" action="">
          <input type="hidden" name="tipo" value="cadastro">
          <input type="text" name="usuario" id="cadastro-usuario" placeholder="Crie um usuário" required>
          <input type="password" name="senha" id="cadastro-senha" placeholder="Crie uma senha" required>
          <button type="submit" id="btn-cadastrar">Cadastrar</button>
        </form>
        <button id="btn-voltar2" class="btn-voltar">⬅ Voltar</button>
        <p>Já tem conta? <a href="#" id="mostrar-login">Voltar ao login</a></p>
      </div>
    </div>
  </div>

  <script>
    // Alternar entre Login e Cadastro
    const formLogin = document.getElementById('form-login');
    const formCadastro = document.getElementById('form-cadastro');
    document.getElementById('mostrar-cadastro').onclick = () => {
      formLogin.style.display = 'none';
      formCadastro.style.display = 'block';
    };
    document.getElementById('mostrar-login').onclick = () => {
      formCadastro.style.display = 'none';
      formLogin.style.display = 'block';
    };
    document.getElementById('btn-voltar2').onclick = () => {
      formCadastro.style.display = 'none';
      formLogin.style.display = 'block';
    };
  </script>
</body>
</html>
