<?php
$caminhoConexao = __DIR__ . "/conexao.php";

if (!file_exists($caminhoConexao)) {
    $caminhoConexao = __DIR__ . "/../conexao.php";
}

if (!file_exists($caminhoConexao)) {
    die("Erro: arquivo conexao.php não foi encontrado. Verifique em qual pasta ele está.");
}

include $caminhoConexao;

if (!isset($conexao) || !$conexao) {
    die("Erro: conexão com o banco não foi carregada.");
}

/*
|--------------------------------------------------------------------------
| ÚLTIMA LEITURA
|--------------------------------------------------------------------------
*/
$tempAtual = 0;
$umidadeAtual = 0;
$setorAtual = "Sem setor";
$temLeitura = false;

$sqlUltima = "
    SELECT l.temperatura, l.umidade, s.nome_setor
    FROM leituras l
    LEFT JOIN setores s ON s.id = l.setor_id
    ORDER BY l.id DESC
    LIMIT 1
";

$resultUltima = $conexao->query($sqlUltima);

if ($resultUltima && $resultUltima->num_rows > 0) {
    $linhaUltima = $resultUltima->fetch_assoc();

    $tempAtual = number_format((float)$linhaUltima["temperatura"], 1, ",", ".");
    $umidadeAtual = number_format((float)$linhaUltima["umidade"], 1, ",", ".");
    $setorAtual = !empty($linhaUltima["nome_setor"]) ? $linhaUltima["nome_setor"] : "Sem setor";
    $temLeitura = true;
}

/*
|--------------------------------------------------------------------------
| TOTAL DE SETORES
|--------------------------------------------------------------------------
*/
$totalSetores = 0;

$sqlSetores = "SELECT COUNT(*) AS total FROM setores";
$resultSetores = $conexao->query($sqlSetores);

if ($resultSetores) {
    $linhaSetores = $resultSetores->fetch_assoc();
    $totalSetores = (int)$linhaSetores["total"];
}

/*
|--------------------------------------------------------------------------
| TOTAL DE ALERTAS
|--------------------------------------------------------------------------
*/
$totalAlertas = 0;

$verificaAlertas = $conexao->query("SHOW TABLES LIKE 'alertas'");

if ($verificaAlertas && $verificaAlertas->num_rows > 0) {
    $sqlAlertas = "SELECT COUNT(*) AS total FROM alertas";
    $resultAlertas = $conexao->query($sqlAlertas);

    if ($resultAlertas) {
        $linhaAlertas = $resultAlertas->fetch_assoc();
        $totalAlertas = (int)$linhaAlertas["total"];
    }
}

/*
|--------------------------------------------------------------------------
| DADOS DO GRÁFICO
|--------------------------------------------------------------------------
*/
$labelsGrafico = [];
$dadosGrafico = [];

$temCreatedAt = false;
$verificaCreatedAt = $conexao->query("SHOW COLUMNS FROM leituras LIKE 'created_at'");

if ($verificaCreatedAt && $verificaCreatedAt->num_rows > 0) {
    $temCreatedAt = true;
}

if ($temCreatedAt) {
    $sqlGrafico = "
        SELECT temperatura, created_at
        FROM leituras
        ORDER BY id DESC
        LIMIT 10
    ";
} else {
    $sqlGrafico = "
        SELECT temperatura
        FROM leituras
        ORDER BY id DESC
        LIMIT 10
    ";
}

$resultGrafico = $conexao->query($sqlGrafico);

if ($resultGrafico) {
    $leituras = [];

    while ($linha = $resultGrafico->fetch_assoc()) {
        $leituras[] = $linha;
    }

    $leituras = array_reverse($leituras);

    foreach ($leituras as $leitura) {
        if ($temCreatedAt && !empty($leitura["created_at"])) {
            $labelsGrafico[] = date("H:i", strtotime($leitura["created_at"]));
        } else {
            $labelsGrafico[] = "";
        }

        $dadosGrafico[] = (float)$leitura["temperatura"];
    }
}
?>