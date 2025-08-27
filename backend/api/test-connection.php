<?php
/**
 * SGQ OTI - Teste de Conexão com Banco de Dados
 */

require_once '../config/cors.php';
require_once '../config/database.php';

try {
    $database = new Database();
    
    // Testar conexão
    $connectionTest = $database->testConnection();
    
    if ($connectionTest['success']) {
        // Criar tabelas se não existirem
        $tablesResult = $database->createTables();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Conexão estabelecida com sucesso',
            'connection' => $connectionTest,
            'tables' => $tablesResult,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Falha na conexão',
            'error' => $connectionTest['message'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
