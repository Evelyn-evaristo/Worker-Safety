<?php
include __DIR__ . "/conexao.php";

$setor_id = isset($_POST['setor_id']) ? (int) $_POST['setor_id'] : null;
$temperatura = isset($_POST['temperatura']) ? (float) $_POST['temperatura'] : null;
$umidade = isset($_POST['umidade']) ? (float) $_POST['umidade'] : null;

if ($setor_id !== null && $temperatura !== null && $umidade !== null) {
    $sql = "INSERT INTO leituras (setor_id, temperatura, umidade) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        die("erro no prepare: " . $conexao->error);
    }

    $stmt->bind_param("idd", $setor_id, $temperatura, $umidade);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "erro ao salvar";
    }

    $stmt->close();
} else {
    echo "dados incompletos";
}

$conexao->close();
?>
