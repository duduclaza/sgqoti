<?php
/**
 * SGQ OTI - API para Configurações do Banco de Dados
 * Endpoint seguro para gerenciar configurações do sistema
 */

require_once '../../config/cors.php';
require_once '../../config/database.php';

// Validar método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetConfig();
            break;
        case 'POST':
            handlePostConfig();
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
    ]);
}

function handleGetConfig() {
    try {
        $database = new Database();
        $connectionTest = $database->testConnection();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Configurações obtidas com sucesso',
            'data' => [
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'u230868210_sgqoti',
                'username' => 'u230868210_dusouza',
                'password_masked' => '***********',
                'connection_status' => $connectionTest['success'],
                'server_time' => $connectionTest['server_time'] ?? null
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao obter configurações',
            'error' => $e->getMessage()
        ]);
    }
}

function handlePostConfig() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação não especificada'
        ]);
        return;
    }
    
    $action = $input['action'];
    
    switch ($action) {
        case 'test_connection':
            testDatabaseConnection();
            break;
        case 'sync_tables':
            syncDatabaseTables();
            break;
        case 'system_check':
            performSystemCheck();
            break;
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: ' . $action
            ]);
            break;
    }
}

function testDatabaseConnection() {
    try {
        $database = new Database();
        $result = $database->testConnection();
        
        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso',
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Falha na conexão com o banco de dados',
                'error' => $result['message'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao testar conexão',
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function syncDatabaseTables() {
    try {
        $database = new Database();
        
        // Primeiro testar conexão
        $connectionTest = $database->testConnection();
        if (!$connectionTest['success']) {
            throw new Exception('Falha na conexão: ' . $connectionTest['message']);
        }
        
        // Criar/atualizar tabelas
        $tablesResult = $database->createTables();
        
        if ($tablesResult['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Tabelas sincronizadas com sucesso',
                'data' => [
                    'connection' => $connectionTest,
                    'tables' => $tablesResult,
                    'tables_created' => [
                        'toners',
                        'movimentacoes_estoque',
                        'usuarios'
                    ]
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao sincronizar tabelas',
                'error' => $tablesResult['message'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao sincronizar tabelas',
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function performSystemCheck() {
    try {
        $database = new Database();
        $checks = [];
        
        // Verificar conexão
        $connectionTest = $database->testConnection();
        $checks['database_connection'] = $connectionTest['success'];
        
        // Verificar tabelas
        if ($connectionTest['success']) {
            $conn = $database->getConnection();
            
            $tables = ['toners', 'movimentacoes_estoque', 'usuarios'];
            $tablesExist = [];
            
            foreach ($tables as $table) {
                $stmt = $conn->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                $tablesExist[$table] = $stmt->rowCount() > 0;
            }
            
            $checks['tables'] = $tablesExist;
        }
        
        // Verificar permissões PHP
        $checks['php_version'] = PHP_VERSION;
        $checks['php_extensions'] = [
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'json' => extension_loaded('json')
        ];
        
        // Verificar permissões de arquivo
        $checks['file_permissions'] = [
            'config_readable' => is_readable('../../config/database.php'),
            'logs_writable' => is_writable('../../logs/') || is_writable('../../')
        ];
        
        $allChecksPass = $connectionTest['success'] && 
                        all_values_true($checks['php_extensions']) &&
                        $checks['file_permissions']['config_readable'];
        
        http_response_code(200);
        echo json_encode([
            'success' => $allChecksPass,
            'message' => $allChecksPass ? 'Sistema funcionando corretamente' : 'Alguns problemas foram detectados',
            'data' => $checks,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao verificar sistema',
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function all_values_true($array) {
    foreach ($array as $value) {
        if (!$value) return false;
    }
    return true;
}
?>
