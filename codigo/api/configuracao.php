<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "worker_safety");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "erro"=>"db"]);
  exit;
}

$setor_id = isset($_GET["setor_id"]) ? intval($_GET["setor_id"]) : 0;
if ($setor_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "erro"=>"setor_id invalido"]);
  exit;
}

$sql = "SELECT c.setor_id, s.nome_setor, c.limite_temp_min, c.limite_temp_max,
               c.limite_umidade_min, c.limite_umidade_max, c.buzzer_ativo, c.led_ativo, c.atualizado_em
        FROM configuracoes c
        JOIN setores s ON s.id = c.setor_id
        WHERE c.setor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $setor_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
  echo json_encode(["ok"=>true] + $row);
} else {
  http_response_code(404);
  echo json_encode(["ok"=>false, "erro"=>"config nao encontrada"]);
}