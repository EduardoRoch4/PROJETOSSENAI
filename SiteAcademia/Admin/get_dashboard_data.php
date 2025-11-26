<?php
// Small admin dashboard API: returns DB counts and recent agendamentos as JSON
header('Content-Type: application/json');

session_start();
// Apenas administradores podem consultar os dados do dashboard
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado: admin requerido.']);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "senaisp";
$db   = "Techfit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message' => 'Conexão falhou: ' . $conn->connect_error]);
    exit;
}

$response = ['status' => 'ok', 'data' => []];

// Counts: usuarios, professor, agendamentos totais, agendamentos futuros
$q = $conn->query("SELECT COUNT(*) AS total_usuarios FROM usuarios");
$response['data']['total_usuarios'] = ($q && $row = $q->fetch_assoc()) ? intval($row['total_usuarios']) : 0;

$q = $conn->query("SELECT COUNT(*) AS total_professores FROM professor");
$response['data']['total_professores'] = ($q && $row = $q->fetch_assoc()) ? intval($row['total_professores']) : 0;

$q = $conn->query("SELECT COUNT(*) AS total_agendamentos FROM agendamentos");
$response['data']['total_agendamentos'] = ($q && $row = $q->fetch_assoc()) ? intval($row['total_agendamentos']) : 0;

$q = $conn->query("SELECT COUNT(*) AS agendamentos_futuros FROM agendamentos WHERE data_hora >= NOW()");
$response['data']['agendamentos_futuros'] = ($q && $row = $q->fetch_assoc()) ? intval($row['agendamentos_futuros']) : 0;

// Recent bookings (join with user name)
$recent = [];
$sql = "SELECT a.id_agendamento, a.data_hora, a.objetivo, a.modalidade, a.status_, u.nome as usuario
        FROM agendamentos a
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
        ORDER BY a.data_hora DESC
        LIMIT 8";
$q = $conn->query($sql);
if ($q) {
    while ($r = $q->fetch_assoc()) {
        // Format but keep raw values
        $recent[] = $r;
    }
}
$response['data']['recent_agendamentos'] = $recent;

$conn->close();

echo json_encode($response, JSON_UNESCAPED_UNICODE);
