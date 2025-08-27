<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Ler dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados JSON inválidos');
    }
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Verificar se tabela de configurações existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'configuracoes'");
    if (!$stmt->fetch()) {
        // Criar tabela de configurações
        $pdo->exec("
            CREATE TABLE configuracoes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                chave VARCHAR(100) UNIQUE NOT NULL,
                valor TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }
    
    // Salvar cada configuração
    $configuracoes = [
        'empresa_nome' => $input['empresa_nome'] ?? 'SGQ OTI',
        'empresa_descricao' => $input['empresa_descricao'] ?? 'Sistema de Gestão da Qualidade',
        'menu_logo_width' => $input['menu_logo_width'] ?? '180',
        'menu_logo_height' => $input['menu_logo_height'] ?? '60',
        'login_logo_width' => $input['login_logo_width'] ?? '180',
        'login_logo_height' => $input['login_logo_height'] ?? '80'
    ];
    
    foreach ($configuracoes as $chave => $valor) {
        $stmt = $pdo->prepare("
            INSERT INTO configuracoes (chave, valor) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE valor = ?, updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$chave, $valor, $valor]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Configurações salvas com sucesso',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar configurações: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
