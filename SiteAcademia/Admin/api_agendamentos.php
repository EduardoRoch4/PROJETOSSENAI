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

// Deletar agendamento
if (isset($data['action']) && $data['action'] === 'delete' && isset($data['id'])) {
    try {
        $id = intval($data['id']);
        $stmt = $conn->prepare("DELETE FROM agendamentos WHERE id_agendamento = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            sendJsonResponse(['status' => 'ok']);
        } else {
            sendJsonResponse(['status' => 'error', 'message' => 'Erro ao deletar: ' . $stmt->error]);
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Erro ao deletar agendamento']);
    }
}

// Atualizar ou inserir agendamento
$usuario = isset($data['usuario']) && $data['usuario'] ? intval($data['usuario']) : null;
$data_hora = $data['data'] ?? '';
$objetivo = $data['objetivo'] ?? '';
$modalidade = $data['modalidade'] ?? '';
$status = $data['status'] ?? 'Confirmado';

// Validar objetivo - deve ser um dos valores do ENUM
$objetivos_validos = ['Perda de peso', 'Ganho de Massa', 'Hipertrofia', 'Saúde'];
if (!in_array($objetivo, $objetivos_validos)) {
    sendJsonResponse(['status' => 'error', 'message' => 'Objetivo inválido. Deve ser um dos: ' . implode(', ', $objetivos_validos)]);
}

// Validar status - deve ser "Confirmado" (único valor do ENUM)
if ($status !== 'Confirmado') {
    $status = 'Confirmado'; // Forçar o valor válido
}

if ($data_hora && $objetivo && $usuario) {
    try {
        // Converter data do formato datetime-local para formato MySQL
        // Exemplo: "2025-12-03T14:30" -> "2025-12-03 14:30:00"
        $datetime_parts = explode('T', $data_hora);
        if (count($datetime_parts) === 2) {
            $data_hora = $datetime_parts[0] . ' ' . $datetime_parts[1] . ':00';
        } else {
            sendJsonResponse(['status' => 'error', 'message' => 'Formato de data inválido']);
        }
        
        if (isset($data['id']) && $data['id']) {
            // Atualizar
            $id = intval($data['id']);
            $stmt = $conn->prepare("UPDATE agendamentos SET id_usuario = ?, data_hora = ?, objetivo = ?, modalidade = ?, status_ = ? WHERE id_agendamento = ?");
            $stmt->bind_param("issssi", $usuario, $data_hora, $objetivo, $modalidade, $status, $id);
            if ($stmt->execute()) {
                sendJsonResponse(['status' => 'ok']);
            } else {
                sendJsonResponse(['status' => 'error', 'message' => 'Erro ao atualizar: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            // Inserir novo
            // Usar id_aula = 1 como placeholder (mesmo padrão usado em processar_agendamento.php)
            $id_aula_placeholder = 1;
            $stmt = $conn->prepare("INSERT INTO agendamentos (id_usuario, data_hora, objetivo, modalidade, status_, id_aula) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $usuario, $data_hora, $objetivo, $modalidade, $status, $id_aula_placeholder);
            if ($stmt->execute()) {
                sendJsonResponse(['status' => 'ok']);
            } else {
                sendJsonResponse(['status' => 'error', 'message' => 'Erro ao inserir: ' . $stmt->error]);
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Erro ao processar agendamento: ' . $e->getMessage()]);
    }
} else {
    sendJsonResponse(['status' => 'error', 'message' => 'Usuário, data e objetivo são obrigatórios']);
}

// Se chegou aqui sem entrar em nenhum caso, retornar erro genérico
sendJsonResponse(['status' => 'error', 'message' => 'Requisição inválida']);
