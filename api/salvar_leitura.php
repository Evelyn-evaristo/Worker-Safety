<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "worker_safety");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "erro"=>"db"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "erro"=>"json invalido"]);
  exit;
}

$setor_id = intval($data["setor_id"] ?? 0);
$temp = floatval($data["temperatura"] ?? 0);
$umid = floatval($data["umidade"] ?? 0);
$alerta = intval($data["alerta_ativo"] ?? 0);
$motivo = $data["motivo_alerta"] ?? null;

if ($setor_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "erro"=>"setor_id invalido"]);
  exit;
}

$sql = "INSERT INTO leituras (setor_id, temperatura, umidade, alerta_ativo, motivo_alerta)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iddis", $setor_id, $temp, $umid, $alerta, $motivo);

if ($stmt->execute()) {
  echo json_encode(["ok"=>true, "id"=>$stmt->insert_id]);
} else {
  http_response_code(500);
  echo json_encode(["ok"=>false, "erro"=>"insert"]);
}