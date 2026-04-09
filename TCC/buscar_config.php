<?php
header('Content-Type: application/json');
include "conexao.php";

$setor_id = $_GET['setor_id'] ?? null;

if ($setor_id === null) {
    echo json_encode(["erro" => "setor_id não informado"]);
    exit;
}

$sql = "SELECT limite_temp_max, limite_temp_min, limite_umidade_max, limite_umidade_min, buzzer_ativo, led_ativo 
        FROM configuracoes 
        WHERE setor_id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    echo json_encode(["erro" => "erro no prepare"]);
    exit;
}

$stmt->bind_param("i", $setor_id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($linha = $resultado->fetch_assoc()) {
    echo json_encode($linha);
} else {
    echo json_encode(["erro" => "configuração não encontrada"]);
}

$stmt->close();
$conexao->close();
?>