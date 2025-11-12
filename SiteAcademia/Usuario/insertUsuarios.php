<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesse este arquivo por um formulário de cadastro.");
}

$nome  = $_POST['nome']  ?? null;
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
$perfil = $_POST['perfil'] ?? null;

 
if (!$nome || !$email || !$senha || $perfil) {
    die("Por favor, preencha todos os campos.");
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Conexão PDO (com senha do banco 'senaisp')
    $conn = new PDO("mysql:host=localhost;dbname=techfit;charset=utf8", "root", "senaisp");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?,?, ?)");
    $resultado = $stmt->execute([$nome, $email, $senha_hash, $perfil]);

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuários</title>
    <link rel="stylesheet" href="usuario.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            padding: 40px;
        }
        .message-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        h2 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .error {
            color: #dc3545;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #0077cc;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <?php
        if (!empty($resultado) && $resultado) {
            echo "<h2>Usuário cadastrado com sucesso!</h2>";
        } else {
            echo "<h2 class='error'>Erro ao cadastrar!</h2>";
        }
        ?>
        <a href="listarUsuarios.php">Voltar para Lista de Usuários</a>
        <br><br>
        <a href="../Index/index.html">Menu Principal</a>
    </div>
</body>
</html>
