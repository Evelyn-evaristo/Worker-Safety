<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/conexao.php";

$setor_id = filter_input(INPUT_GET, 'setor_id', FILTER_VALIDATE_INT);

if ($setor_id === false || $setor_id === null) {
    http_response_code(400);
    echo json_encode(["erro" => "setor_id inválido ou não informado"], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$sql = "SELECT limite_temp_max, limite_temp_min, limite_umidade_max, limite_umidade_min, buzzer_ativo, led_ativo 
        FROM configuracoes 
        WHERE setor_id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["erro" => "erro no prepare", "detalhe" => $conexao->error], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param("i", $setor_id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($linha = $resultado->fetch_assoc()) {
    echo json_encode($linha, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["erro" => "configuração não encontrada"], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();
?>