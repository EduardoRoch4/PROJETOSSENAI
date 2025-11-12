<?php
include '../PROJETOSSENAI/SiteAcademia/conectar.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Aluno</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container">
    <nav>
      <a href="listarAlunos.php">Listar Alunos</a>
      <a href="../index.html">Menu Principal</a>
    </nav>

    <h2>Cadastro de Aluno</h2>
    <form action="insertAlunos.php" method="POST">
      <label>Nome:</label>
      <input type="text" name="nome" required>

      <label>Data de Nascimento:</label>
      <input type="date" name="data_nascimento" id="data_nascimento" required>

      <label>Email:</label>
      <input type="email" name="email" required pattern="^[^@]+@[^@]+\.[a-zA-Z]{2,}$" 
             title="Digite um email válido (ex: usuario@dominio.com)">

      <label>Telefone:</label>
      <input type="text" name="telefone" maxlength="11" pattern="[0-9]{11}" required>


      <label>Curso:</label>
      <?php
      // Debug: testar conexão e consulta
      $sql = "SELECT id_curso, nome FROM cursos ORDER BY nome";
      $result = $conn->query($sql);
      if (!$result) {
          echo "<p style='color:red'>Erro SQL: " . $conn->error . "</p>";
      } else {
          echo "<p style='color:green'>Cursos encontrados: " . $result->num_rows . "</p>";
      }
      ?>
      <select name="id_curso" required>
        <option value="">Selecione um curso</option>
        <?php
        if ($result && $result->num_rows > 0) {
          while ($curso = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($curso['id_curso'], ENT_QUOTES) . "'>" . 
                 htmlspecialchars($curso['nome'], ENT_QUOTES) . "</option>";
          }
        } else {
          echo "<option value=''>Nenhum curso encontrado</option>";
        }
        ?>
      </select>

      <button type="submit">Enviar</button>
    </form>
  </div>

<script>
const today = new Date().toISOString().split("T")[0];
document.getElementById("data_nascimento").setAttribute("max", today);
</script>
</body>
</html>
<?php $conn->close(); ?>
