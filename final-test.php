<?php
session_start();

// Teste simples e direto das funcionalidades do admin
echo "<!DOCTYPE html><html><head><title>Diagnóstico Final Admin</title>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>🔧 Diagnóstico Final - Admin MIA</h1>";

// Teste 1: Arquivos essenciais
echo "<h2>1. Arquivos Essenciais</h2>";
$arquivos = [
    'admin.php' => 'Painel Admin Principal',
    'admin-mensagens.php' => 'Gestão de Mensagens',
    'admin_actions.php' => 'Ações AJAX do Admin',
    'config.php' => 'Configurações',
    'config/produtos.php' => 'Configuração de Produtos'
];

foreach ($arquivos as $arquivo => $desc) {
    $existe = file_exists($arquivo);
    $class = $existe ? 'ok' : 'error';
    $icon = $existe ? '✅' : '❌';
    echo "<p class='$class'>$icon $desc ($arquivo)</p>";
}

// Teste 2: Diretórios e permissões
echo "<h2>2. Diretórios e Permissões</h2>";
$dirs = ['data', 'uploads', 'config'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $write = is_writable($dir) ? '✅' : '❌';
        $read = is_readable($dir) ? '✅' : '❌';
        echo "<p class='ok'>📁 $dir - Leitura: $read | Escrita: $write</p>";
    } else {
        echo "<p class='error'>❌ Diretório $dir não existe</p>";
    }
}

// Teste 3: URLs funcionais
echo "<h2>3. Teste de URLs</h2>";

// Função para fazer requisição HTTP simples
function testar_url($url_path, $post_data = null)
{
    $url = "http://localhost/VOUEXPLODIR/$url_path";
    $context = stream_context_create([
        'http' => [
            'method' => $post_data ? 'POST' : 'GET',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $post_data ? http_build_query($post_data) : '',
            'timeout' => 5
        ]
    ]);

    $result = @file_get_contents($url, false, $context);
    return $result !== false;
}

$urls_teste = [
    'admin_actions.php?action=test' => 'Teste de Conexão Admin',
    'api.php?action=search_products&term=test' => 'API de Busca',
    'admin.php' => 'Painel Admin',
    'admin-mensagens.php' => 'Mensagens Admin'
];

foreach ($urls_teste as $url => $desc) {
    $funciona = testar_url($url);
    $class = $funciona ? 'ok' : 'error';
    $icon = $funciona ? '✅' : '❌';
    echo "<p class='$class'>$icon $desc</p>";
    echo "<p><small>URL: <a href='$url' target='_blank'>$url</a></small></p>";
}

// Teste 4: Dados JSON
echo "<h2>4. Dados JSON</h2>";
$jsons = [
    'data/produtos.json' => 'Produtos',
    'data/mensagens.json' => 'Mensagens',
    'data/produto-destaque.json' => 'Produto Destaque'
];

foreach ($jsons as $arquivo => $desc) {
    if (file_exists($arquivo)) {
        $content = file_get_contents($arquivo);
        $valid = json_decode($content, true) !== null;
        $class = $valid ? 'ok' : 'error';
        $icon = $valid ? '✅' : '❌';
        echo "<p class='$class'>$icon $desc - JSON " . ($valid ? 'válido' : 'inválido') . "</p>";
    } else {
        echo "<p class='error'>❌ $desc - Arquivo não existe</p>";
    }
}

// Teste 5: Configuração PHP  
echo "<h2>5. Configuração PHP</h2>";
echo "<p class='ok'>✅ Versão PHP: " . phpversion() . "</p>";
echo "<p class='ok'>✅ Sessões: " . (session_status() === PHP_SESSION_ACTIVE ? 'Ativas' : 'Inativas') . "</p>";
echo "<p class='ok'>✅ JSON: " . (function_exists('json_encode') ? 'Disponível' : 'Não disponível') . "</p>";

// Instruções finais
echo "<h2>🎯 Instruções de Uso</h2>";
echo "<ol>";
echo "<li><strong>Acesse o Admin:</strong> <a href='admin.php' target='_blank'>http://localhost/VOUEXPLODIR/admin.php</a></li>";
echo "<li><strong>Credenciais:</strong> Usuário: <code>admin</code> | Senha: <code>mia2025</code></li>";
echo "<li><strong>Mensagens:</strong> <a href='admin-mensagens.php' target='_blank'>http://localhost/VOUEXPLODIR/admin-mensagens.php</a></li>";
echo "<li><strong>Site Principal:</strong> <a href='index.php' target='_blank'>http://localhost/VOUEXPLODIR/index.php</a></li>";
echo "</ol>";

echo "<h2>⚠️ Se ainda houver erros:</h2>";
echo "<ul>";
echo "<li>Verifique se o XAMPP está rodando (Apache + MySQL)</li>";
echo "<li>Confira o arquivo php-error.log para erros específicos</li>";
echo "<li>Teste as URLs individualmente nos links acima</li>";
echo "<li>Certifique-se de que o diretório tem permissões corretas</li>";
echo "</ul>";

echo "</body></html>";
?>