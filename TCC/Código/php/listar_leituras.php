<?php
header('Content-Type: application/json');
include "conexao.php";

$sql = "
    SELECT l.id, l.setor_id, l.temperatura, l.umidade, l.created_at, s.nome_setor
    FROM leituras l
    LEFT JOIN setores s ON s.id = l.setor_id
    ORDER BY l.id DESC
    LIMIT 50
";

$resultado = $conexao->query($sql);

$leituras = [];

if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $leituras[] = $linha;
    }
}

echo json_encode($leituras, JSON_UNESCAPED_UNICODE);

$conexao->close();
?>