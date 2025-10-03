<?php
// Configurações de URLs para o admin
define("BASE_URL", "http://localhost/VOUEXPLODIR/");
define("ADMIN_BASE", BASE_URL . "admin.php");
define("ADMIN_MENSAGENS", BASE_URL . "admin-mensagens.php");
define("ADMIN_ACTIONS", BASE_URL . "admin_actions.php");
define("API_URL", BASE_URL . "api.php");

// Função para resolver URLs
function resolve_admin_url($page, $params = []) {
    $base_urls = [
        "admin" => "admin.php",
        "mensagens" => "admin-mensagens.php", 
        "actions" => "admin_actions.php",
        "api" => "api.php"
    ];
    
    $url = $base_urls[$page] ?? $page;
    
    if (!empty($params)) {
        $url .= "?" . http_build_query($params);
    }
    
    return $url;
}
?>