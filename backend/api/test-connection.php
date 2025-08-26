<?php
require_once '../config/cors.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo json_encode(array(
        "status" => "success",
        "message" => "Conexão com banco de dados estabelecida com sucesso!",
        "database" => "u230868210_sgqoti",
        "timestamp" => date('Y-m-d H:i:s')
    ));
} else {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => "Falha na conexão com banco de dados"
    ));
}
?>
