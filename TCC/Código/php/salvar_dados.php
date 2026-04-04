<?php
include "conexao.php";

$setor_id = $_POST['setor_id'] ?? null;
$temperatura = $_POST['temperatura'] ?? null;
$umidade = $_POST['umidade'] ?? null;

if ($setor_id && $temperatura && $umidade) {
    $sql = "INSERT INTO leituras (setor_id, temperatura, umidade) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("idd", $setor_id, $temperatura, $umidade);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "erro";
    }

    $stmt->close();
} else {
    echo "dados incompletos";
}

$conexao->close();
?>