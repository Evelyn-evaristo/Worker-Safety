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
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="style/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-layout">

            <div class="sidebar-header">
                <div class="user-box">
                    <div class="user-avatar">
                        <img src="img/logo.png" alt="Usuário">
                    </div>

                    <div class="user-info">
                        <h1>Worker Safety</h1>
                        <p>Painel de Controle</p>
                    </div>
                </div>

                <button id="btnSidebar" class="btn-sidebar" type="button">☰</button>
            </div>

            <div class="sidebar-content">
                <nav class="menu">
                    <ul>
                        <li class="menu-header">
                            <span>DASHBOARDS</span>
                        </li>

                        <li class="menu-item">
                            <a href="index.php">
                                <span class="menu-icon">
                                    <img src="img/Dashboard.png" alt="Dashboard">
                                </span>
                                <span class="menu-title">Overview</span>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="temperatura.php">
                                <span class="menu-icon">
                                    <img src="img/Temperatura.png" alt="Temperatura">
                                </span>
                                <span class="menu-title">Temperatura</span>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="umidade.php">
                                <span class="menu-icon">
                                    <img src="img/Umidade.png" alt="Umidade">
                                </span>
                                <span class="menu-title">Umidade</span>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="alertas.php">
                                <span class="menu-icon">
                                    <img src="img/Alertas.png" alt="Alertas">
                                </span>
                                <span class="menu-title">Alertas</span>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="setores.php">
                                <span class="menu-icon">
                                    <img src="img/Setores.png" alt="Setores">
                                </span>
                                <span class="menu-title">Setores</span>
                            </a>
                        </li>

                        <li class="menu-header second-header">
                            <span>SETTINGS</span>
                        </li>

                        <li class="menu-item">
                            <a href="configuracoes.php">
                                <span class="menu-icon">
                                    <img src="img/Configuracao.png" alt="Configurações">
                                </span>
                                <span class="menu-title">Configurações</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="footer-logo">
                    <span class="footer-dot"></span>
                    <span class="footer-text">Worker Safety</span>
                </div>
            </div>

        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <h1>Dashboard Geral</h1>

        <div class="dashboard">
            <div class="cards">
                <div class="card">🌡️ Temp: <span id="temp"><?= $tempAtual ?></span>°C</div>
                <div class="card">💧 Umidade: <span id="hum"><?= $umidadeAtual ?></span>%</div>
                <div class="card">🚨 Alertas: <span id="alert"><?= $totalAlertas ?></span></div>
                <div class="card">🏢 Setores: <?= $totalSetores ?></div>
            </div>

            <div class="dashboard-grid">
                <div class="chart-box">
                    <h3>Temperatura <?= $temLeitura ? "- " . htmlspecialchars($setorAtual) : "" ?></h3>
                    <div class="chart-area">
                        <canvas id="tempChart"></canvas>
                    </div>
                </div>

                <div class="alerts-box">
                    <h3>Resumo</h3>
                    <ul id="alertList">
                        <?php if ($temLeitura): ?>
                            <li>Última leitura registrada no setor: <?= htmlspecialchars($setorAtual) ?></li>
                            <li>Temperatura atual: <?= $tempAtual ?> °C</li>
                            <li>Umidade atual: <?= $umidadeAtual ?> %</li>
                        <?php else: ?>
                            <li>Ainda não existem leituras cadastradas no banco.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.graficoTemperatura = {
            labels: <?= json_encode($labelsGrafico, JSON_UNESCAPED_UNICODE) ?>,
            dados: <?= json_encode($dadosGrafico, JSON_UNESCAPED_UNICODE) ?>
        };
    </script>
    <script src="script.js"></script>
</body>
</html>