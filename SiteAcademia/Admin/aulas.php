<?php
session_start();
// Só admins podem visualizar o painel
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
  header('Location: /Login/login.php');
    exit;
}
$adminName = isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Admin';

$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar todas as aulas
$aulas = [];
$query = "SELECT a.id_aula, a.nome, a.descricao, a.horario, p.nome as professor_nome 
          FROM aulas a 
          LEFT JOIN professor p ON a.id_professor = p.id_professor 
          ORDER BY a.nome ASC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $aulas[] = $row;
    }
}

// Buscar lista de professores para seleção
$professores = [];
$query_prof = "SELECT id_professor, nome FROM professor ORDER BY nome ASC";
$result_prof = $conn->query($query_prof);
if ($result_prof) {
    while ($row = $result_prof->fetch_assoc()) {
        $professores[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Aulas | TechFit Admin</title>
  <link rel="stylesheet" href="painel.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="logo">
      <img src="../IMG/Logo.png" alt="Logo TechFit">
    </div>

    <nav class="nav-buttons" id="nav-buttons">
      <a href="../index.html">Início</a>
      <a href="../Agendamento/Agendamento.html">Agendamento</a>
      <a href="../Unidades/Unidades.html">Unidades</a>
      <a href="../Chat/chat.html">Chat</a>
      <a href="../Admin/painel.php">Painel Admin</a>
      <a href="../Nossa História/nos.html">Sobre Nós</a>
      <a href="../Alunos/usuario.php" id="perfil-btn" style="display:none;">Perfil</a>
      <a href="../Login/login.php" id="login-btn">Login</a>
      <span id="user-display" style="display:none;margin-left:12px;color:#fff;">Olá, <strong id="user-name"><?php echo htmlspecialchars($adminName); ?></strong></span>
    </nav>

    <div class="menu-icon" id="menu-icon">☰</div>
  </header>

  <div class="side-menu" id="side-menu">
    <div class="close-btn" id="close-btn">✖</div>
      <a href="../index.html">Início</a>
      <a href="../Agendamento/Agendamento.html">Agendamento</a>
      <a href="../Unidades/Unidades.html">Unidades</a>
      <a href="../Chat/chat.html">Chat</a>
      <a href="../Admin/painel.php">Painel Admin</a>
      <a href="../Nossa História/nos.html">Sobre Nós</a>
      <a href="../Alunos/usuario.php" id="perfil-btn" style="display:none">Perfil</a>
      <a href="../Login/login.php" id="login-btn">Login</a>
  </div>

  <div class="overlay" id="overlay"></div>

  <div class="app-layout">
    <aside class="sidebar">
      <div class="sidebar-top">
        <img src="../IMG/Logo.png" alt="TechFit" class="sidebar-logo">
        <h3>TechFit Admin</h3>
      </div>

      <nav class="sidebar-nav">
        <a href="painel.php">Visão Geral</a>
        <a href="alunos.php">Alunos</a>
        <a href="professores.php">Professores</a>
        <a class="active" href="aulas.php">Aulas</a>
        <a href="agendamentos.php">Agendamentos</a>
        <a href="relatorios.php">Relatórios</a>
      </nav>

      <div class="sidebar-footer">
        <small>Usuário: <strong><?php echo $adminName; ?></strong></small>
        <a href="/Login/login.php?acao=logout" class="btn-logout">Sair</a>
      </div>
    </aside>

    <main class="dashboard">
      <header class="dashboard-header">
        <div class="dash-title">
          <h1>Gerenciamento de Aulas</h1>
          <p class="muted">Visualize e edite informações das aulas cadastradas</p>
        </div>
        <div class="dash-actions">
          <input id="search-input" placeholder="Pesquisar aula..." />
          <button class="btn small" id="nova-aula">+ Nova Aula</button>
        </div>
      </header>

      <section class="recent-section">
        <div class="section-header">
          <h2>Aulas Cadastradas</h2>
          <small id="count-aulas" class="muted"><?php echo count($aulas); ?> aulas</small>
        </div>
        <div class="table-wrap">
          <table class="recent-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Horário</th>
                <th>Professor</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody id="aulas-table-body">
              <?php if (empty($aulas)): ?>
                <tr><td colspan="6" class="muted">Nenhuma aula encontrada</td></tr>
              <?php else: ?>
                <?php foreach ($aulas as $aula): ?>
                  <tr>
                    <td>#<?php echo htmlspecialchars($aula['id_aula']); ?></td>
                    <td><?php echo htmlspecialchars($aula['nome']); ?></td>
                    <td><?php echo htmlspecialchars($aula['descricao'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($aula['horario'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($aula['professor_nome'] ?? '—'); ?></td>
                    <td>
                      <button class="btn-action editar" data-id="<?php echo htmlspecialchars($aula['id_aula']); ?>">Editar</button>
                      <button class="btn-action deletar" data-id="<?php echo htmlspecialchars($aula['id_aula']); ?>">Deletar</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close" id="close">&times;</span>
      <h2 id="modal-titulo">Editar Aula</h2>
      <form id="form-aula">
        <input type="hidden" id="aula-id">
        <input type="text" id="aula-nome" placeholder="Nome da Aula" required>
        <textarea id="aula-descricao" placeholder="Descrição" rows="3"></textarea>
        <input type="time" id="aula-horario" placeholder="Horário">
        <select id="aula-professor">
          <option value="">Selecione um professor</option>
          <?php foreach ($professores as $prof): ?>
            <option value="<?php echo htmlspecialchars($prof['id_professor']); ?>">
              <?php echo htmlspecialchars($prof['nome']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Salvar</button>
      </form>
    </div>
  </div>

<footer class="fade-in-up">
  <p>© 2025 TechFit — Todos os direitos reservados</p>
</footer>

  <script src="admin.js"></script>
</body>
</html>
