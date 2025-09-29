<?php
// ACESSO RESTRITO - Página apenas para administradores
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se está logado como admin
$admin_logged = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'];

// Se não estiver logado, redirecionar para página de login
if (!$admin_logged) {
    header('Location: admin');
    exit();
}

// Verificar se foi enviado um ID de mensagem para excluir
if (!isset($_POST['message_id']) || empty($_POST['message_id'])) {
    header('Location: admin-mensagens?error=1');
    exit();
}

$message_id = $_POST['message_id'];

// Carregar mensagens
$arquivo_mensagens = 'data/mensagens.json';
$mensagens = [];

if (file_exists($arquivo_mensagens)) {
    $conteudo = file_get_contents($arquivo_mensagens);
    $mensagens = json_decode($conteudo, true) ?: [];
}

// Encontrar e remover a mensagem
$mensagem_encontrada = false;
foreach ($mensagens as $key => $mensagem) {
    if ($mensagem['id'] === $message_id) {
        unset($mensagens[$key]);
        $mensagem_encontrada = true;
        break;
    }
}

if ($mensagem_encontrada) {
    // Reindexar o array
    $mensagens = array_values($mensagens);
    
    // Salvar as mensagens atualizadas
    $json_atualizado = json_encode($mensagens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($arquivo_mensagens, $json_atualizado);
    
    // Redirecionar para a página admin de mensagens
    header('Location: admin-mensagens?deleted=1');
} else {
    // Redirecionar para a página admin de mensagens com erro
    header('Location: admin-mensagens?error=2');
}

exit();
?>
