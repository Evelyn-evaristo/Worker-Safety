<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/conexao.php";

$setor_id = isset($_POST['setor_id']) ? (int) $_POST['setor_id'] : null;
$temperatura = isset($_POST['temperatura']) ? (float) $_POST['temperatura'] : null;
$umidade = isset($_POST['umidade']) ? (float) $_POST['umidade'] : null;

if ($setor_id === null || $temperatura === null || $umidade === null) {
    http_response_code(400);
    echo json_encode(["erro" => "dados incompletos"], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$sql = "INSERT INTO leituras (setor_id, temperatura, umidade) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["erro" => "erro no prepare", "detalhe" => $conexao->error], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param("idd", $setor_id, $temperatura, $umidade);

if ($stmt->execute()) {
    echo json_encode(["mensagem" => "ok"], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "erro ao salvar", "detalhe" => $stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();
?>