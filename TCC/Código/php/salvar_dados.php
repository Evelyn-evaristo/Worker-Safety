<?php
$caminhoConexao = __DIR__ . "/../../conexao.php";

if (!file_exists($caminhoConexao)) {
    http_response_code(500);
    echo "erro: arquivo conexao.php não encontrado";
    exit;
}

include $caminhoConexao;

$setor_id = isset($_POST['setor_id']) ? (int) $_POST['setor_id'] : null;
$temperatura = isset($_POST['temperatura']) ? (float) $_POST['temperatura'] : null;
$umidade = isset($_POST['umidade']) ? (float) $_POST['umidade'] : null;

if ($setor_id !== null && $temperatura !== null && $umidade !== null) {
    $sql = "INSERT INTO leituras (setor_id, temperatura, umidade) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo "erro no prepare: " . $conexao->error;
        exit;
    }

    $stmt->bind_param("idd", $setor_id, $temperatura, $umidade);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        http_response_code(500);
        echo "erro ao salvar";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "dados incompletos";
}

$conexao->close();
?>
