<?php
include "conexao.php";

$setor_id = $_POST['setor_id'] ?? null;
$temp_max = $_POST['limite_temp_max'] ?? null;
$temp_min = $_POST['limite_temp_min'] ?? null;
$umidade_max = $_POST['limite_umidade_max'] ?? null;
$umidade_min = $_POST['limite_umidade_min'] ?? null;
$buzzer_ativo = $_POST['buzzer_ativo'] ?? 1;
$led_ativo = $_POST['led_ativo'] ?? 1;

if ($setor_id && $temp_max !== null && $temp_min !== null && $umidade_max !== null && $umidade_min !== null) {
    $sql = "UPDATE configuracoes 
            SET limite_temp_max = ?, limite_temp_min = ?, limite_umidade_max = ?, limite_umidade_min = ?, buzzer_ativo = ?, led_ativo = ?
            WHERE setor_id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ddddiii", $temp_max, $temp_min, $umidade_max, $umidade_min, $buzzer_ativo, $led_ativo, $setor_id);

    if ($stmt->execute()) {
        echo "Configuração atualizada com sucesso";
    } else {
        echo "Erro ao atualizar";
    }

    $stmt->close();
} else {
    echo "Dados incompletos";
}

$conexao->close();
?>