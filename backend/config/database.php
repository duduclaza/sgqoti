<?php
/**
 * SGQ OTI - Configuração do Banco de Dados
 * PHP 8.4 + MariaDB
 */

class Database {
    private $host = "srv2020.hstgr.io";
    private $db_name = 'u230868210_sgqoti';
    private $username = 'u230868210_dusouza';
    private $password = 'Pandora@1989';
    private $port = 3306;
    private $charset = 'utf8mb4';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Configurar timezone
            $this->conn->exec("SET time_zone = '-03:00'");
            
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Erro de conexão com o banco de dados");
        }

        return $this->conn;
    }

    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1 as test, NOW() as server_time");
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso',
                'server_time' => $result['server_time'],
                'database' => $this->db_name
            ];
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createTables() {
        try {
            $conn = $this->getConnection();
            $results = [];
            
            // Definir esquema das tabelas
            $tables = $this->getTableSchema();
            
            foreach ($tables as $tableName => $tableSQL) {
                try {
                    $conn->exec($tableSQL);
                    $results[] = "Tabela '$tableName' criada/atualizada com sucesso";
                } catch(Exception $e) {
                    $results[] = "Erro na tabela '$tableName': " . $e->getMessage();
                }
            }

            return [
                'success' => true,
                'message' => 'Sincronização concluída',
                'details' => $results
            ];
            
        } catch(Exception $e) {
            error_log("Error creating tables: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar tabelas: ' . $e->getMessage()
            ];
        }
    }

    public function syncTables() {
        try {
            $conn = $this->getConnection();
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            
            // Primeiro, criar tabelas básicas se não existirem
            $basicResult = $this->createTables();
            if (!$basicResult['success']) {
                throw new Exception($basicResult['message']);
            }
            
            // Analisar tabelas existentes
            $existingTables = $this->getExistingTables();
            $schemaChanges = $this->analyzeSchemaChanges($existingTables);
            
            // Aplicar mudanças necessárias
            foreach ($schemaChanges as $change) {
                try {
                    $conn->exec($change['sql']);
                    $results[] = "✅ " . $change['description'];
                    $successCount++;
                } catch(Exception $e) {
                    $results[] = "❌ Erro: " . $change['description'] . " - " . $e->getMessage();
                    $errorCount++;
                    error_log("SQL Error: " . $change['sql'] . " - " . $e->getMessage());
                }
            }

            if (empty($schemaChanges)) {
                $results[] = "✅ Banco de dados já está atualizado";
            }

            return [
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Sincronização concluída com sucesso' : 'Sincronização concluída com alguns erros',
                'details' => $results,
                'changes_applied' => $successCount,
                'errors' => $errorCount
            ];
            
        } catch(Exception $e) {
            error_log("Error syncing tables: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao analisar banco: ' . $e->getMessage(),
                'details' => ["❌ " . $e->getMessage()]
            ];
        }
    }

    private function getTableSchema() {
        return [
            'toners' => "CREATE TABLE IF NOT EXISTS toners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                codigo VARCHAR(50) NOT NULL UNIQUE,
                modelo VARCHAR(100) NOT NULL,
                marca VARCHAR(50) NOT NULL,
                cor VARCHAR(30) NOT NULL,
                tipo ENUM('original', 'compativel', 'remanufaturado') NOT NULL,
                quantidade_estoque INT DEFAULT 0,
                quantidade_minima INT DEFAULT 5,
                preco_unitario DECIMAL(10,2) DEFAULT 0.00,
                fornecedor VARCHAR(100),
                observacoes TEXT,
                status ENUM('ativo', 'inativo') DEFAULT 'ativo',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_codigo (codigo),
                INDEX idx_modelo (modelo),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'movimentacoes_estoque' => "CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
                id INT AUTO_INCREMENT PRIMARY KEY,
                toner_id INT NOT NULL,
                tipo_movimentacao ENUM('entrada', 'saida', 'ajuste') NOT NULL,
                quantidade INT NOT NULL,
                quantidade_anterior INT NOT NULL,
                quantidade_atual INT NOT NULL,
                motivo VARCHAR(200),
                usuario VARCHAR(100),
                data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (toner_id) REFERENCES toners(id) ON DELETE CASCADE,
                INDEX idx_toner_id (toner_id),
                INDEX idx_data (data_movimentacao)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'usuarios' => "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                usuario VARCHAR(50) NOT NULL UNIQUE,
                senha_hash VARCHAR(255) NOT NULL,
                perfil ENUM('admin', 'user', 'viewer') DEFAULT 'user',
                status ENUM('active', 'inactive') DEFAULT 'active',
                ultimo_acesso TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_usuario (usuario),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
    }

    private function getExistingTables() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SHOW TABLES");
            $tables = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tableName = $row[0];
                $tables[$tableName] = $this->getTableStructure($tableName);
            }
            
            return $tables;
        } catch(Exception $e) {
            return [];
        }
    }

    private function getTableStructure($tableName) {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("DESCRIBE `$tableName`");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return [];
        }
    }

    private function analyzeSchemaChanges($existingTables) {
        $changes = [];
        $expectedTables = $this->getTableSchema();
        
        foreach ($expectedTables as $tableName => $tableSQL) {
            if (!isset($existingTables[$tableName])) {
                $changes[] = [
                    'sql' => $tableSQL,
                    'description' => "Criando tabela '$tableName'"
                ];
            } else {
                // Verificar se precisa de alterações na estrutura
                $alterations = $this->checkTableAlterations($tableName, $existingTables[$tableName]);
                $changes = array_merge($changes, $alterations);
            }
        }
        
        return $changes;
    }

    private function checkTableAlterations($tableName, $currentStructure) {
        $changes = [];
        
        // Verificar se a tabela usuarios tem o campo 'usuario'
        if ($tableName === 'usuarios') {
            $hasUsuarioField = false;
            foreach ($currentStructure as $column) {
                if ($column['Field'] === 'usuario') {
                    $hasUsuarioField = true;
                    break;
                }
            }
            
            if (!$hasUsuarioField) {
                $changes[] = [
                    'sql' => "ALTER TABLE usuarios ADD COLUMN usuario VARCHAR(50) AFTER email",
                    'description' => "Adicionando campo 'usuario' na tabela usuarios"
                ];
                $changes[] = [
                    'sql' => "ALTER TABLE usuarios ADD UNIQUE INDEX idx_usuario (usuario)",
                    'description' => "Adicionando índice único para campo 'usuario'"
                ];
            }
        }
        
        return $changes;
    }
}
?>
