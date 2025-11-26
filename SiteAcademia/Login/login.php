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
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$mensagem = "";

// Código secreto para criação pública de contas administrativas (mude em produção)
$ADMIN_CREATION_CODE = 'techfit-admin-2025';

// ====== CADASTRO DE USUÁRIO ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] === 'cadastro') {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);

    $perfil = $_POST['perfil'] ?? 'aluno';
    $perfil = in_array($perfil, ['aluno','admin']) ? $perfil : 'aluno';

    // Se a inscrição pede perfil admin, só permitimos quando:
    //  - o usuário logado é admin (criação por admin), OU
    //  - o código secreto de criação de admin é fornecido e corresponde
    $permit_create_admin = false;
    if ($perfil === 'admin') {
      // Usuário logado com perfil admin pode criar administradores
      if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'admin') {
        $permit_create_admin = true;
      } else {
        // Verifica código de criação enviado pelo formulário
        $codigo_admin = trim($_POST['codigo_admin'] ?? '');
        if (!empty($codigo_admin) && $codigo_admin === $ADMIN_CREATION_CODE) {
          $permit_create_admin = true;
        }
      }
    } else {
      $permit_create_admin = true; // perfil 'aluno' sempre permitido
    }

    if (!empty($usuario) && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        if (!$permit_create_admin) {
          $mensagem = "❌ Você não tem permissão para criar uma conta administrativa (código inválido).";
        } else {
          $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $usuario, $usuario, $senhaHash, $perfil);

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
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TechFit</title>
  <link rel="stylesheet" type="text/css" href="../Login/login.css">
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
        <p>Não tem conta? <a href="#" id="mostrar-cadastro">Cadastre-se</a></p>
      </div>

      <!-- ===== FORM CADASTRO ===== -->
      <div id="form-cadastro" style="display:none;">
        <form method="POST" action="">
          <input type="hidden" name="tipo" value="cadastro">
          <input type="text" name="usuario" id="cadastro-usuario" placeholder="Crie um usuário" required>
          <input type="password" name="senha" id="cadastro-senha" placeholder="Crie uma senha" required>
          <!-- Sempre exigir que usuário escolha perfil ao se cadastrar -->
          <label for="perfil" style="display:block;margin:8px 0 4px;font-size:14px;">Perfil</label>
          <select name="perfil" id="perfil" style="padding:8px;border-radius:6px;margin-bottom:8px;" required>
            <option value="aluno">Aluno</option>
            <option value="admin">Administrativo</option>
          </select>

          <!-- Campo de código admin (aparece quando 'Administrativo' selecionado e usuário não é admin logado) -->
          <div id="codigo-block" style="display:none; margin-bottom:8px;">
            <label for="codigo_admin" style="display:block;margin:8px 0 4px;font-size:13px;">Código secreto para criar Administrador</label>
            <input type="text" name="codigo_admin" id="codigo_admin" placeholder="Código administrativo (obrigatório para criar admin)" style="padding:8px;border-radius:6px;">
          </div>
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

    // Mostrar/ocultar campo de código admin conforme seleção de perfil
    const perfilSelect = document.getElementById('perfil');
    const codigoBlock = document.getElementById('codigo-block');
    const codigoInput = document.getElementById('codigo_admin');

    function toggleCodigo() {
      if (!perfilSelect) return;
      const selected = perfilSelect.value;
      // Se selecionou admin e não está logado como admin, mostramos o campo e tornamos requerido
      const isAdminSelected = (selected === 'admin');
      // If session profile is admin, we don't need the code input (server will allow creation)
      const isLoggedAdmin = <?php echo (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'admin') ? 'true' : 'false'; ?>;
      if (isAdminSelected && !isLoggedAdmin) {
        codigoBlock.style.display = 'block';
        codigoInput.required = true;
      } else {
        codigoBlock.style.display = 'none';
        if (codigoInput) codigoInput.required = false;
      }
    }

    if (perfilSelect) {
      perfilSelect.addEventListener('change', toggleCodigo);
      // Initialize on load
      toggleCodigo();
    }

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
