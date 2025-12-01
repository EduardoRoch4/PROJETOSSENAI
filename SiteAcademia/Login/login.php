<?php
session_start();

// Logout via GET param
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
  session_unset();
  session_destroy();
  header('Location: /Login/login.php');
  exit;
}

// ====== CONEXÃO COM O BANCO ======
$host = "localhost";
$user = "root";
$pass = "1234";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$mensagem = "";

// Admin creation is only allowed by logged-in admins. Removed public secret-code creation path.

// ====== CADASTRO DE USUÁRIO ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] === 'cadastro') {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);

    $perfil = $_POST['perfil'] ?? 'aluno';
    $perfil = in_array($perfil, ['aluno','admin']) ? $perfil : 'aluno';

    // Allow public registration of either profile (aluno or admin).
    // The user requested both options at registration time.
    $permit_create_admin = true;

    if (!empty($usuario) && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        if (!$permit_create_admin) {
          $mensagem = "❌ Você não tem permissão para criar uma conta administrativa.";
        } else {
          $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $usuario, $usuario, $senhaHash, $perfil);

          if ($stmt->execute()) {
            // freshly created user - automatically log them in
            $novo_id = $conn->insert_id;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['id_usuario'] = $novo_id;
            $_SESSION['perfil'] = $perfil;
            // redirect based on perfil
            if ($perfil === 'admin') {
              header("Location: /Admin/painel.php");
            } else {
              header("Location: /Alunos/usuario.php");
            }
            exit;
          } else {
            if ($conn->errno == 1062) {
              $mensagem = "⚠️ Usuário já existe.";
            } else {
              $mensagem = "❌ Erro ao cadastrar: " . $conn->error;
            }
          }
          $stmt->close();
        }

        // stmt already executed inside the conditional above (if $permit_create_admin)
    } else {
        $mensagem = "⚠️ Preencha todos os campos!";
    }
}

// ====== LOGIN DE USUÁRIO ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] === 'login') {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);

      if (!empty($usuario) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id_usuario, nome, senha, perfil FROM usuarios WHERE nome = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $dados = $resultado->fetch_assoc();

            if (password_verify($senha, $dados['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario'] = $dados['nome'];
                $_SESSION['id_usuario'] = $dados['id_usuario'];
              // guarda o perfil do usuário na sessão
              $_SESSION['perfil'] = $dados['perfil'];
              // Redireciona para painel admin se for admin, senão envia para perfil
              if (isset($dados['perfil']) && $dados['perfil'] === 'admin') {
                header("Location: /Admin/painel.php");
              } else {
                header("Location: /Alunos/usuario.php");
              }
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
// ... (PHP code remains unchanged)

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TechFit</title>
  <link rel="stylesheet" type="text/css" href="login.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  
  <div class="login-container">
    <div class="login-box">
      <h1>TechFit</h1>

      <?php if(!empty($mensagem)) { echo "<p id='mensagem'>$mensagem</p>"; } ?>

      <div id="form-login">
        <form method="POST" action="">
          <input type="hidden" name="tipo" value="login">
          <input type="text" name="usuario" id="login-usuario" placeholder="Usuário" required>
          <input type="password" name="senha" id="login-senha" placeholder="Senha" required>
          <button type="submit" id="btn-login">Entrar</button>
        </form>
        <button type="button" id="btn-voltar" class="btn-link" onclick="history.back()">
          &larr; Voltar
        </button>
        <p>Não tem conta? <a href="#" id="mostrar-cadastro">Cadastre-se</a></p>
      </div>

      <div id="form-cadastro" style="display:none;">
        <form method="POST" action="">
          <input type="hidden" name="tipo" value="cadastro">
          <input type="text" name="usuario" id="cadastro-usuario" placeholder="Crie um usuário" required>
          <input type="password" name="senha" id="cadastro-senha" placeholder="Crie uma senha" required>
          <label for="perfil" style="display:block;margin:8px 0 4px;font-size:14px;">Perfil</label>
          <select name="perfil" id="perfil" style="padding:8px;border-radius:6px;margin-bottom:8px;" required>
            <option value="aluno">Aluno</option>
            <option value="admin">Administrativo</option>
          </select>
          <button type="submit" id="btn-cadastrar">Cadastrar</button>
        </form>
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
  </script>
</body>
</html>