<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Criar tabela de logos se não existir
    $createTable = "
        CREATE TABLE IF NOT EXISTS logos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            tipo ENUM('sidebar', 'header', 'login') NOT NULL,
            arquivo_nome VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            tamanho INT NOT NULL,
            dados LONGBLOB NOT NULL,
            ativo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_tipo_ativo (tipo, ativo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $conn->exec($createTable);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['tipo'])) {
                // Buscar logo por tipo
                $stmt = $conn->prepare("SELECT * FROM logos WHERE tipo = ? AND ativo = TRUE ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$_GET['tipo']]);
                $logo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($logo && isset($_GET['download'])) {
                    // Servir a imagem
                    header('Content-Type: ' . $logo['mime_type']);
                    header('Content-Length: ' . $logo['tamanho']);
                    header('Content-Disposition: inline; filename="' . $logo['arquivo_nome'] . '"');
                    echo $logo['dados'];
                    exit;
                }
                
                if ($logo) {
                    // Remover dados binários da resposta JSON
                    unset($logo['dados']);
                    $logo['url'] = "backend/api/logos.php?tipo={$logo['tipo']}&download=1";
                }
                
                echo json_encode(['success' => true, 'logo' => $logo]);
            } else {
                // Listar todos os logos
                $stmt = $conn->prepare("SELECT id, nome, tipo, arquivo_nome, mime_type, tamanho, ativo, created_at FROM logos ORDER BY tipo, created_at DESC");
                $stmt->execute();
                $logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($logos as &$logo) {
                    $logo['url'] = "backend/api/logos.php?tipo={$logo['tipo']}&download=1";
                }
                
                echo json_encode(['success' => true, 'logos' => $logos]);
            }
            break;
            
        case 'POST':
            // Upload de novo logo
            if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Erro no upload do arquivo');
            }
            
            $file = $_FILES['logo'];
            $tipo = $_POST['tipo'] ?? 'sidebar';
            $nome = $_POST['nome'] ?? 'Logo ' . ucfirst($tipo);
            
            // Validar tipo de arquivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF, WebP ou SVG.');
            }
            
            // Validar tamanho (máximo 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Arquivo muito grande. Máximo 5MB.');
            }
            
            // Ler dados do arquivo
            $dados = file_get_contents($file['tmp_name']);
            
            // Desativar logo anterior do mesmo tipo
            $stmt = $conn->prepare("UPDATE logos SET ativo = FALSE WHERE tipo = ? AND ativo = TRUE");
            $stmt->execute([$tipo]);
            
            // Inserir novo logo
            $stmt = $conn->prepare("
                INSERT INTO logos (nome, tipo, arquivo_nome, mime_type, tamanho, dados, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, TRUE)
            ");
            $stmt->execute([
                $nome,
                $tipo,
                $file['name'],
                $file['type'],
                $file['size'],
                $dados
            ]);
            
            $logoId = $conn->lastInsertId();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Logo enviado com sucesso',
                'logo_id' => $logoId,
                'url' => "backend/api/logos.php?tipo={$tipo}&download=1"
            ]);
            break;
            
        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID do logo não fornecido');
            }
            
            $stmt = $conn->prepare("DELETE FROM logos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['success' => true, 'message' => 'Logo removido com sucesso']);
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
