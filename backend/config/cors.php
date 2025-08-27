<?php
/**
 * SGQ OTI - Configuração CORS
 * Permite requisições do frontend
 */

// Configuração de CORS para permitir requisições do frontend
$allowed_origins = [
    'http://localhost',
    'http://127.0.0.1',
    'https://lightseagreen-cobra-261680.hostingersite.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Responder a requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
