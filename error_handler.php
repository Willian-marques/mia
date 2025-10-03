<?php
// Handler de erro silencioso para requisições indevidas
header('Content-Type: application/json');
http_response_code(204); // No Content - requisição processada mas sem resposta
echo '';
exit;
?>