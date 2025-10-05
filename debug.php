<?php
header('Content-Type: text/plain');
print_r([
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? null,
    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? null,
    'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'] ?? null,
    'HTTPS' => $_SERVER['HTTPS'] ?? null,
    'X_FORWARDED_PROTO' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
    'X_FORWARDED_PREFIX' => $_SERVER['HTTP_X_FORWARDED_PREFIX'] ?? null,
    'X_FORWARDED_SERVER' => $_SERVER['HTTP_X_FORWARDED_SERVER'] ?? null,
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? null,
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
]);