<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/conexao.php";

$sql = "
    SELECT l.id, l.setor_id, l.temperatura, l.umidade, l.created_at, s.nome_setor
    FROM leituras l
    LEFT JOIN setores s ON s.id = l.setor_id
    ORDER BY l.id DESC
    LIMIT 50
";

$resultado = $conexao->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(["erro" => "erro ao listar leituras", "detalhe" => $conexao->error], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$leituras = [];

while ($linha = $resultado->fetch_assoc()) {
    $leituras[] = $linha;
}

echo json_encode($leituras, JSON_UNESCAPED_UNICODE);

$conexao->close();
?>