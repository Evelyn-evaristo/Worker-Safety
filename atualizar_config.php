<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/conexao.php";

$setor_id = isset($_POST['setor_id']) ? (int) $_POST['setor_id'] : null;
$temp_max = isset($_POST['limite_temp_max']) ? (float) $_POST['limite_temp_max'] : null;
$temp_min = isset($_POST['limite_temp_min']) ? (float) $_POST['limite_temp_min'] : null;
$umidade_max = isset($_POST['limite_umidade_max']) ? (float) $_POST['limite_umidade_max'] : null;
$umidade_min = isset($_POST['limite_umidade_min']) ? (float) $_POST['limite_umidade_min'] : null;
$buzzer_ativo = isset($_POST['buzzer_ativo']) ? (int) $_POST['buzzer_ativo'] : 1;
$led_ativo = isset($_POST['led_ativo']) ? (int) $_POST['led_ativo'] : 1;

if ($setor_id === null || $temp_max === null || $temp_min === null || $umidade_max === null || $umidade_min === null) {
    http_response_code(400);
    echo json_encode(["erro" => "dados incompletos"], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$sql = "UPDATE configuracoes 
        SET limite_temp_max = ?, limite_temp_min = ?, limite_umidade_max = ?, limite_umidade_min = ?, buzzer_ativo = ?, led_ativo = ?
        WHERE setor_id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["erro" => "erro no prepare", "detalhe" => $conexao->error], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param("ddddiii", $temp_max, $temp_min, $umidade_max, $umidade_min, $buzzer_ativo, $led_ativo, $setor_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["erro" => "setor não encontrado"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["mensagem" => "configuração atualizada com sucesso"], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(500);
    echo json_encode(["erro" => "erro ao atualizar", "detalhe" => $stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();
?>