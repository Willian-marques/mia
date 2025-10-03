<?php
// Debug para admin_actions.php
session_start();

echo "<!DOCTYPE html><html><head><title>Debug Admin Actions</title></head><body>";
echo "<h1>🔍 Debug Admin Actions</h1>";

echo "<h2>📍 Informações da Sessão:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Admin Logged:</strong> " . (isset($_SESSION['admin_logged']) ? ($_SESSION['admin_logged'] ? 'SIM' : 'NÃO') : 'NÃO DEFINIDO') . "</p>";

echo "<h2>🔗 Teste de URLs:</h2>";
$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

// Definir sessão como logada para teste
$_SESSION['admin_logged'] = true;

echo "<p><strong>Base URL:</strong> $base_url</p>";
echo "<p><a href='admin_actions.php?action=test' target='_blank'>Teste admin_actions.php</a></p>";

// Teste direto da função
echo "<h2>📦 Teste Direto:</h2>";
try {
    require_once 'config/produtos.php';
    $produtos = getAllProdutos();
    echo "<p><strong>Total de produtos:</strong> " . count($produtos) . "</p>";
    
    if (!empty($produtos)) {
        $primeiro_produto = array_values($produtos)[0];
        echo "<p><strong>Primeiro produto ID:</strong> " . $primeiro_produto['id'] . "</p>";
        echo "<p><a href='admin_actions.php?action=get&productId=" . $primeiro_produto['id'] . "' target='_blank'>Teste get produto</a></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>