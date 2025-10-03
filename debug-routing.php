<?php
// Debug de roteamento para resolver erros 404
echo "<!DOCTYPE html><html><head><title>Debug Roteamento</title></head><body>";
echo "<h1>🔍 Debug de Roteamento - Site Hospedado</h1>";

echo "<h2>📍 Informações da Requisição:</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'não definido') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'não definido') . "</p>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'não definido') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'não definido') . "</p>";

echo "<h2>🔗 Teste de URLs Importantes:</h2>";

$urls_teste = [
    'index.php' => 'Página Principal',
    'produtos.php' => 'Catálogo de Produtos',
    'produto-unico.php?id=1' => 'Produto Específico (ID: 1)',
    'admin.php' => 'Painel Admin',
    'admin-mensagens.php' => 'Mensagens Admin'
];

foreach ($urls_teste as $url => $desc) {
    $link_completo = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/" . $url;
    echo "<p><strong>$desc:</strong> <a href='$url' target='_blank'>$url</a></p>";
}

echo "<h2>📦 Verificação de Produtos:</h2>";
require_once 'config/produtos.php';

$todos_produtos = getAllProdutos('ativo');
echo "<p><strong>Total de produtos ativos:</strong> " . count($todos_produtos) . "</p>";

if (!empty($todos_produtos)) {
    echo "<h3>Produtos Encontrados:</h3>";
    foreach ($todos_produtos as $produto) {
        echo "<p>ID: {$produto['id']} - {$produto['title']} - Status: {$produto['status']}</p>";
        echo "<p><a href='produto-unico.php?id={$produto['id']}' target='_blank'>Ver produto</a></p>";
    }
} else {
    echo "<p style='color: red;'>Nenhum produto ativo encontrado!</p>";
}

echo "<h2>⚙️ Verificação de Arquivos:</h2>";
$arquivos_importantes = [
    'produto-unico.php',
    'produtos.php',
    'index.php',
    'admin.php',
    '.htaccess',
    'config/produtos.php',
    'data/produtos.json'
];

foreach ($arquivos_importantes as $arquivo) {
    $existe = file_exists($arquivo);
    $cor = $existe ? 'green' : 'red';
    $status = $existe ? '✅ Existe' : '❌ Não existe';
    echo "<p style='color: $cor;'><strong>$arquivo:</strong> $status</p>";
}

echo "<h2>🧪 Teste Direto de Funções:</h2>";
try {
    $produto_teste = getProdutoById(1);
    if ($produto_teste) {
        echo "<p style='color: green;'>✅ Função getProdutoById(1) funcionando: " . $produto_teste['title'] . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Produto ID 1 não encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na função getProdutoById: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>