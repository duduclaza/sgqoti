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
                'host' => 'srv2020.hstgr.io',
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
        case 'save_config':
            saveConfiguration($input);
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
        
        // Sincronizar tabelas com análise
        $tablesResult = $database->syncTables();
        
        if ($tablesResult['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Tabelas sincronizadas com sucesso',
                'data' => [
                    'connection' => $connectionTest,
                    'tables' => $tablesResult,
                    'details' => $tablesResult['details'] ?? [],
                    'changes_applied' => $tablesResult['changes_applied'] ?? 0
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

function saveConfiguration($input) {
    try {
        if (!isset($input['config'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Dados de configuração não fornecidos'
            ]);
            return;
        }
        
        $config = $input['config'];
        
        // Validar campos obrigatórios
        $requiredFields = ['host', 'port', 'database', 'username', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($config[$field]) || empty(trim($config[$field]))) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Campo obrigatório ausente: {$field}"
                ]);
                return;
            }
        }
        
        // Validar porta
        $port = intval($config['port']);
        if ($port <= 0 || $port > 65535) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Porta deve ser um número entre 1 e 65535'
            ]);
            return;
        }
        
        // Testar conexão com as novas configurações
        $testConnection = testNewConnection($config);
        if (!$testConnection['success']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Falha ao conectar com as novas configurações: ' . $testConnection['message']
            ]);
            return;
        }
        
        // Salvar configurações no arquivo
        $configFileContent = generateConfigFile($config);
        $configFilePath = '../../config/database.php';
        
        if (file_put_contents($configFilePath, $configFileContent) === false) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao salvar arquivo de configuração'
            ]);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Configurações salvas com sucesso',
            'data' => [
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'connection_test' => $testConnection
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao salvar configurações',
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function testNewConnection($config) {
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
        
        // Testar com uma query simples
        $stmt = $pdo->query("SELECT NOW() as server_time, VERSION() as mysql_version");
        $result = $stmt->fetch();
        
        return [
            'success' => true,
            'message' => 'Conexão estabelecida com sucesso',
            'server_time' => $result['server_time'],
            'mysql_version' => $result['mysql_version']
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erro de conexão: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erro inesperado: ' . $e->getMessage()
        ];
    }
}

function generateConfigFile($config) {
    $content = '<?php
/**
 * SGQ OTI - Configuração do Banco de Dados
 * Arquivo gerado automaticamente em ' . date('Y-m-d H:i:s') . '
 */

class Database {
    private $host = "' . addslashes($config['host']) . '";
    private $port = "' . addslashes($config['port']) . '";
    private $db_name = "' . addslashes($config['database']) . '";
    private $username = "' . addslashes($config['username']) . '";
    private $password = "' . addslashes($config['password']) . '";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            throw $exception;
        }
        
        return $this->conn;
    }
    
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query("SELECT NOW() as server_time, VERSION() as mysql_version");
                $result = $stmt->fetch();
                
                return [
                    \'success\' => true,
                    \'message\' => \'Conexão estabelecida com sucesso\',
                    \'server_time\' => $result[\'server_time\'],
                    \'mysql_version\' => $result[\'mysql_version\']
                ];
            }
        } catch (Exception $e) {
            return [
                \'success\' => false,
                \'message\' => $e->getMessage()
            ];
        }
    }
    
    public function createTables() {
        try {
            $conn = $this->getConnection();
            
            // Tabela de toners
            $sql_toners = "CREATE TABLE IF NOT EXISTS toners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                marca VARCHAR(100) NOT NULL,
                modelo VARCHAR(100) NOT NULL,
                cor VARCHAR(50) NOT NULL,
                codigo VARCHAR(100),
                quantidade INT DEFAULT 0,
                estoque_minimo INT DEFAULT 5,
                localizacao VARCHAR(100),
                observacoes TEXT,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_marca (marca),
                INDEX idx_modelo (modelo),
                INDEX idx_cor (cor)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql_toners);
            
            // Tabela de movimentações de estoque
            $sql_movimentacoes = "CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
                id INT AUTO_INCREMENT PRIMARY KEY,
                toner_id INT NOT NULL,
                tipo_movimentacao ENUM(\'entrada\', \'saida\') NOT NULL,
                quantidade INT NOT NULL,
                motivo VARCHAR(255),
                usuario VARCHAR(100),
                data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (toner_id) REFERENCES toners(id) ON DELETE CASCADE,
                INDEX idx_toner_id (toner_id),
                INDEX idx_data (data_movimentacao)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql_movimentacoes);
            
            // Tabela de usuários (para futuras funcionalidades)
            $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                nivel_acesso ENUM(\'admin\', \'usuario\') DEFAULT \'usuario\',
                ativo BOOLEAN DEFAULT TRUE,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_ultimo_acesso TIMESTAMP NULL,
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql_usuarios);
            
            return [
                \'success\' => true,
                \'message\' => \'Tabelas criadas com sucesso\'
            ];
            
        } catch (Exception $e) {
            return [
                \'success\' => false,
                \'message\' => $e->getMessage()
            ];
        }
    }
}
?>';
    
    return $content;
}

function all_values_true($array) {
    foreach ($array as $value) {
        if (!$value) return false;
    }
    return true;
}
?>
