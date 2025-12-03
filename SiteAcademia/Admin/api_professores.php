<?php
session_start();
header('Content-Type: application/json');

// Verificar se é admin
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado']);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Erro na conexão: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Deletar professor
if (isset($data['action']) && $data['action'] === 'delete' && isset($data['id'])) {
    $id = intval($data['id']);
    
    // Primeiro, deletar aulas associadas
    $stmt_del_aulas = $conn->prepare("DELETE FROM aulas WHERE id_professor = ?");
    $stmt_del_aulas->bind_param("i", $id);
    $stmt_del_aulas->execute();
    $stmt_del_aulas->close();
    
    // Depois, deletar o professor
    $stmt = $conn->prepare("DELETE FROM professor WHERE id_professor = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao deletar: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Atualizar ou inserir professor
$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';
$especialidade = $data['especialidade'] ?? '';

if ($nome && $email) {
    if (isset($data['id']) && $data['id']) {
        // Atualizar
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE professor SET nome = ?, email = ?, especialidade = ? WHERE id_professor = ?");
        $stmt->bind_param("sssi", $nome, $email, $especialidade, $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        // Inserir novo
        $stmt = $conn->prepare("INSERT INTO professor (nome, email, especialidade) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $especialidade);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nome e email são obrigatórios']);
}

$conn->close();
