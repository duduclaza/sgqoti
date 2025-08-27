<?php
/**
 * SGQ OTI - Configuração do Banco de Dados
 * PHP 8.4 + MariaDB
 */

class Database {
    private $host = 'localhost';
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
            $stmt = $conn->query("SELECT 1 as test, NOW() as current_time");
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso',
                'server_time' => $result['current_time'],
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
            
            // Tabela de toners
            $sql = "CREATE TABLE IF NOT EXISTS toners (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql);

            // Tabela de movimentações de estoque
            $sql = "CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql);

            // Tabela de usuários (para futuro sistema de login)
            $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                senha_hash VARCHAR(255) NOT NULL,
                perfil ENUM('admin', 'usuario', 'visualizador') DEFAULT 'usuario',
                status ENUM('ativo', 'inativo') DEFAULT 'ativo',
                ultimo_acesso TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql);

            return [
                'success' => true,
                'message' => 'Tabelas criadas com sucesso'
            ];
            
        } catch(Exception $e) {
            error_log("Error creating tables: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao criar tabelas: ' . $e->getMessage()
            ];
        }
    }
}
?>
