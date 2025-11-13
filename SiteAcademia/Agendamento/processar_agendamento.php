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

// 3. VALIDAR DADOS
if (empty($dia) || empty($mes) || empty($ano) || empty($horario) || empty($objetivo)) {
    $response['message'] = '⚠️ Por favor, preencha todos os campos.';
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
$status_confirmado = "Confirmado"; //

$stmt = $conn->prepare("INSERT INTO agendamentos (data_hora, objetivo, status_, id_aula, id_usuario) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $data_hora_sql, $objetivo, $status_confirmado, $id_aula_placeholder, $id_usuario);

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = '✅ Agendamento realizado com sucesso!';
} else {
    $response['message'] = '❌ Erro ao salvar no banco: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// 6. RETORNAR A RESPOSTA PARA O JAVASCRIPT
header('Content-Type: application/json');
echo json_encode($response);
?>