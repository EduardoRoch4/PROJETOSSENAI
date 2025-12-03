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

// Deletar aula
if (isset($data['action']) && $data['action'] === 'delete' && isset($data['id'])) {
    $id = intval($data['id']);
    $stmt = $conn->prepare("DELETE FROM aulas WHERE id_aula = ?");
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

// Atualizar ou inserir aula
$nome = $data['nome'] ?? '';
$descricao = $data['descricao'] ?? '';
$horario = $data['horario'] ?? null;
$professor = isset($data['professor']) && $data['professor'] ? intval($data['professor']) : null;

if ($nome) {
    if (isset($data['id']) && $data['id']) {
        // Atualizar
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE aulas SET nome = ?, descricao = ?, horario = ?, id_professor = ? WHERE id_aula = ?");
        $stmt->bind_param("sssii", $nome, $descricao, $horario, $professor, $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        // Inserir novo
        $stmt = $conn->prepare("INSERT INTO aulas (nome, descricao, horario, id_professor) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nome, $descricao, $horario, $professor);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nome da aula é obrigatório']);
}

$conn->close();
