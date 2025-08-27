<?php
require_once '../config/cors.php';
require_once '../config/database.php';
require_once '../utils/logger.php';

header('Content-Type: application/json');

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);
    
    $action = $input['action'] ?? '';
    
    if ($action === 'drop_old_tables') {
        dropOldTables();
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação não reconhecida'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}

function dropOldTables() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        Logger::log('dropOldTables started');
        
        // Verificar se as tabelas existem antes de tentar remover
        $tables = ['movimentacoes_estoque', 'toners'];
        $droppedTables = [];
        
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            
            if ($stmt->rowCount() > 0) {
                // Tabela existe, remover
                $conn->exec("DROP TABLE IF EXISTS $table");
                $droppedTables[] = $table;
                Logger::log("Table dropped: $table");
            } else {
                Logger::log("Table not found: $table");
            }
        }
        
        // Verificar tabelas restantes
        $stmt = $conn->query("SHOW TABLES");
        $remainingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        Logger::log('dropOldTables completed', [
            'dropped' => $droppedTables,
            'remaining' => $remainingTables
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Tabelas antigas removidas com sucesso',
            'data' => [
                'dropped_tables' => $droppedTables,
                'remaining_tables' => $remainingTables
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        Logger::log('dropOldTables exception', ['error' => $e->getMessage()], 'ERROR');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao remover tabelas: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
?>
