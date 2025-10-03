<?php
// Diagnóstico completo de URLs e roteamento

session_start();
$_SESSION['admin_logged'] = true; // Temporariamente definir como logado para teste

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Diagnóstico de URLs</title></head><body>\n";
echo "<h1>Diagnóstico Completo - Admin URLs</h1>\n";

// Função para testar URL
function testarURL($url, $metodo = 'GET', $dados = null)
{
    $context = stream_context_create([
        'http' => [
            'method' => $metodo,
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $dados ? http_build_query($dados) : ''
        ]
    ]);

    $resultado = @file_get_contents("http://localhost/VOUEXPLODIR/$url", false, $context);
    $headers = $http_response_header ?? [];

    return [
        'sucesso' => $resultado !== false,
        'conteudo' => $resultado,
        'headers' => $headers
    ];
}

echo "<h2>Teste de URLs Admin:</h2>\n";

// URLs para testar
$urls_teste = [
    'admin.php' => 'GET',
    'admin-mensagens.php' => 'GET',
    'admin_actions.php?action=test' => 'GET',
    'api.php?action=search_products&term=bolsa' => 'GET',
    'delete-message.php' => 'POST'
];

foreach ($urls_teste as $url => $metodo) {
    echo "<h3>Testando: $url ($metodo)</h3>\n";

    $dados = null;
    if ($metodo === 'POST' && strpos($url, 'delete-message') !== false) {
        $dados = ['message_id' => 'test123'];
    }

    $resultado = testarURL($url, $metodo, $dados);

    if ($resultado['sucesso']) {
        echo "<p style='color: green'>✅ Sucesso</p>\n";
        if (strpos($resultado['conteudo'], 'error') !== false || strpos($resultado['conteudo'], 'erro') !== false) {
            echo "<p style='color: orange'>⚠️ Contém erros no conteúdo</p>\n";
        }
    } else {
        echo "<p style='color: red'>❌ Falhou</p>\n";
    }

    // Mostrar headers para debug
    if (!empty($resultado['headers'])) {
        $status_line = $resultado['headers'][0] ?? 'Sem status';
        echo "<p><small>Status: $status_line</small></p>\n";
    }

    // Mostrar preview do conteúdo (primeiros 200 chars)
    if ($resultado['conteudo']) {
        $preview = substr(strip_tags($resultado['conteudo']), 0, 200);
        echo "<p><small>Preview: " . htmlspecialchars($preview) . "...</small></p>\n";
    }

    echo "<hr>\n";
}

// Teste específico do admin_actions.php
echo "<h2>Teste Específico admin_actions.php:</h2>\n";

$acoes_teste = ['test', 'connection_test', 'test1'];
foreach ($acoes_teste as $acao) {
    $url = "admin_actions.php?action=$acao";
    $resultado = testarURL($url);

    $status = $resultado['sucesso'] ? '✅' : '❌';
    echo "<p>$status Action '$acao': ";

    if ($resultado['conteudo']) {
        $json = json_decode($resultado['conteudo'], true);
        if ($json) {
            echo "JSON válido - " . ($json['success'] ? 'Sucesso' : 'Erro: ' . ($json['error'] ?? 'desconhecido'));
        } else {
            echo "Resposta não-JSON: " . htmlspecialchars(substr($resultado['conteudo'], 0, 100));
        }
    } else {
        echo "Sem resposta";
    }
    echo "</p>\n";
}

// Verificar se o problema é de roteamento
echo "<h2>Verificação de Roteamento:</h2>\n";

echo "<p>REQUEST_URI atual: " . ($_SERVER['REQUEST_URI'] ?? 'não definido') . "</p>\n";
echo "<p>SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'não definido') . "</p>\n";
echo "<p>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'não definido') . "</p>\n";
echo "<p>DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'não definido') . "</p>\n";

// Verificar .htaccess
if (file_exists('.htaccess')) {
    echo "<p>✅ .htaccess existe</p>\n";
    $htaccess_content = file_get_contents('.htaccess');
    echo "<pre>" . htmlspecialchars($htaccess_content) . "</pre>\n";
} else {
    echo "<p>ℹ️ .htaccess não existe</p>\n";
}

echo "</body></html>\n";
?>