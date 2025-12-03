<?php
// Desabilitar exibição de erros ANTES de qualquer output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Iniciar output buffering IMEDIATAMENTE
if (!ob_get_level()) {
    ob_start();
}

// Função para retornar JSON de forma segura
function sendJsonResponse($data) {
    // Limpar qualquer output anterior
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Tratamento de erros fatais
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Erro fatal no servidor']);
        exit;
    }
});

// Iniciar sessão silenciosamente
try {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
} catch (Exception $e) {
    sendJsonResponse(['status' => 'error', 'message' => 'Erro ao iniciar sessão']);
}

// Verificar se é admin
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    sendJsonResponse(['status' => 'error', 'message' => 'Acesso negado']);
}

$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        sendJsonResponse(['status' => 'error', 'message' => 'Erro na conexão: ' . $conn->connect_error]);
    }
    $conn->set_charset("utf8");
} catch (Exception $e) {
    sendJsonResponse(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados']);
}

try {
    $input = file_get_contents('php://input');
    $data = null;

    // Tentar decodificar JSON apenas se houver input
    if (!empty($input)) {
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendJsonResponse(['status' => 'error', 'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()]);
        }
    }

    // Se não houver dados após decodificação, retornar erro
    if ($data === null || !is_array($data)) {
        sendJsonResponse(['status' => 'error', 'message' => 'Nenhum dado válido recebido']);
    }
} catch (Exception $e) {
    sendJsonResponse(['status' => 'error', 'message' => 'Erro ao processar requisição']);
}

// Deletar aluno
if (isset($data['action']) && $data['action'] === 'delete' && isset($data['id'])) {
    try {
        $id = intval($data['id']);
        
        // Desabilitar verificação de foreign keys temporariamente
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Deletar de mensagens (remetente e destinatário)
        $stmt_msg1 = $conn->prepare("DELETE FROM mensagens WHERE id_usuario_remetente = ? OR id_usuario_destinatario = ?");
        $stmt_msg1->bind_param("ii", $id, $id);
        $stmt_msg1->execute();
        $stmt_msg1->close();
        
        // Deletar de outras tabelas
        $stmt_ag = $conn->prepare("DELETE FROM agendamentos WHERE id_usuario = ?");
        $stmt_ag->bind_param("i", $id);
        $stmt_ag->execute();
        $stmt_ag->close();
        
        $stmt_av = $conn->prepare("DELETE FROM avaliacoes_fisicas WHERE id_usuario = ?");
        $stmt_av->bind_param("i", $id);
        $stmt_av->execute();
        $stmt_av->close();
        
        $stmt_ac = $conn->prepare("DELETE FROM acessos WHERE id_usuario = ?");
        $stmt_ac->bind_param("i", $id);
        $stmt_ac->execute();
        $stmt_ac->close();
        
        $stmt_pag = $conn->prepare("DELETE FROM pagamentos WHERE id_usuario = ?");
        $stmt_pag->bind_param("i", $id);
        $stmt_pag->execute();
        $stmt_pag->close();
        
        // Depois, deletar o aluno
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Reabilitar verificação de foreign keys
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            sendJsonResponse(['status' => 'ok']);
        } else {
            // Reabilitar verificação de foreign keys mesmo em caso de erro
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            sendJsonResponse(['status' => 'error', 'message' => 'Erro ao deletar: ' . $stmt->error]);
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Reabilitar verificação de foreign keys em caso de exceção
        try {
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $e2) {}
        sendJsonResponse(['status' => 'error', 'message' => 'Erro ao deletar aluno: ' . $e->getMessage()]);
    }
}

// Atualizar ou inserir aluno
$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';

if ($nome && $email) {
    try {
        if (isset($data['id']) && $data['id']) {
            // Atualizar
            $id = intval($data['id']);
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id_usuario = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
            if ($stmt->execute()) {
                sendJsonResponse(['status' => 'ok']);
            } else {
                sendJsonResponse(['status' => 'error', 'message' => $stmt->error]);
            }
            $stmt->close();
        } else {
            // Inserir novo
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, perfil) VALUES (?, ?, 'aluno')");
            $stmt->bind_param("ss", $nome, $email);
            if ($stmt->execute()) {
                sendJsonResponse(['status' => 'ok']);
            } else {
                sendJsonResponse(['status' => 'error', 'message' => $stmt->error]);
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Erro ao processar aluno: ' . $e->getMessage()]);
    }
} else {
    sendJsonResponse(['status' => 'error', 'message' => 'Nome e email são obrigatórios']);
}

$conn->close();
