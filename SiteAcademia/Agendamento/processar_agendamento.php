<?php
session_start();

// Resposta padrão
$response = ['status' => 'error', 'message' => 'Erro desconhecido.'];

// 1. VERIFICAR SE O USUÁRIO ESTÁ LOGADO
if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Usuário não autenticado. Faça login novamente.';
    echo json_encode($response);
    exit;
}

// 2. OBTER DADOS ENVIADOS PELO JAVASCRIPT
// O JS enviará os dados como JSON, então lemos o input
$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $_SESSION['id_usuario'];
$dia = $data['dia'] ?? null;
$mes = $data['mes'] ?? null; // 1-12
$ano = $data['ano'] ?? null;
$horario = $data['horario'] ?? null; // "08:00"
$objetivo = $data['objetivo'] ?? null; // "Perda de peso"
$modalidade = $data['modalidade'] ?? null; // "Musculação"

// 3. VALIDAR DADOS
if (empty($dia) || empty($mes) || empty($ano) || empty($horario) || empty($objetivo) || empty($modalidade)) {
    $response['message'] = '⚠️ Por favor, preencha todos os campos (incluindo modalidade).';
    echo json_encode($response);
    exit;
}

// 4. FORMATAR DATA E HORA PARA O SQL (DATETIME YYYY-MM-DD HH:MM:SS)
try {
    // Formata a data: "YYYY-MM-DD"
    $data_sql = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
    // Formata a hora: "HH:MM:SS"
    $hora_sql = $horario . ":00";
    // Combina:
    $data_hora_sql = $data_sql . " " . $hora_sql;
} catch (Exception $e) {
    $response['message'] = '❌ Erro ao formatar a data.';
    echo json_encode($response);
    exit;
}

// 5. CONECTAR AO BANCO E INSERIR
$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $response['message'] = '❌ Erro de conexão com o banco.';
    echo json_encode($response);
    exit;
}

// Usamos 1 como placeholder para id_aula, já que não é selecionado
$id_aula_placeholder = 1;
$status_confirmado = "Confirmado";

// 5.1 — Verificar se a coluna 'modalidade' existe (para armazenar separadamente)
$stmt_check = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'agendamentos' AND COLUMN_NAME = 'modalidade'");
$stmt_check->bind_param('s', $db);
$stmt_check->execute();
$res = $stmt_check->get_result()->fetch_row();
$has_modalidade_col = (isset($res[0]) && intval($res[0]) > 0);
$stmt_check->close();

if ($has_modalidade_col) {
    // Insere com coluna modalidade separada
    $stmt = $conn->prepare("INSERT INTO agendamentos (data_hora, objetivo, modalidade, status_, id_aula, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response['message'] = '❌ Erro ao preparar a query: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("ssssii", $data_hora_sql, $objetivo, $modalidade, $status_confirmado, $id_aula_placeholder, $id_usuario);
} else {
    // Se não existe coluna modalidade, NÃO anexamos a modalidade ao ENUM objetivo — isso quebra o enum
    // Em vez disso, armazenamos apenas o objetivo válido (pois o ENUM não aceita valores concatenados)
    $stmt = $conn->prepare("INSERT INTO agendamentos (data_hora, objetivo, status_, id_aula, id_usuario) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response['message'] = '❌ Erro ao preparar a query: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("sssii", $data_hora_sql, $objetivo, $status_confirmado, $id_aula_placeholder, $id_usuario);
}

try {
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = '✅ Agendamento realizado com sucesso!';
        // If the table doesn't have a modalidade column, let caller know it wasn't saved
        if (!$has_modalidade_col) {
            $response['message'] .= ' (Observação: modalidade não foi gravada — tabela não possui coluna `modalidade`).';
        }
    } else {
        $response['message'] = '❌ Erro ao salvar no banco: ' . $stmt->error;
    }
} catch (mysqli_sql_exception $ex) {
    // Evita que a aplicação quebre com stack traces — retornamos JSON limpo com a mensagem
    $response['message'] = '❌ Exceção ao salvar no banco: ' . $ex->getMessage();
}

$stmt->close();
$conn->close();

// 6. RETORNAR A RESPOSTA PARA O JAVASCRIPT
header('Content-Type: application/json');
echo json_encode($response);
?>