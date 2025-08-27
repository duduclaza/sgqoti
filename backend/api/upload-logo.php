<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    if (!isset($_FILES['logo']) || !isset($_POST['type'])) {
        throw new Exception('Arquivo ou tipo não fornecido');
    }
    
    $file = $_FILES['logo'];
    $type = $_POST['type']; // 'menu' ou 'login'
    
    // Validar arquivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload do arquivo');
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Arquivo muito grande. Máximo 2MB');
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido. Use PNG, JPG ou SVG');
    }
    
    // Definir nome do arquivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $type === 'menu' ? 'logo.png' : 'logo-login.png';
    
    // Criar diretório se não existir
    $uploadDir = '../../assets/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    // Mover arquivo
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Erro ao salvar arquivo');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Logo enviada com sucesso',
        'filename' => $filename,
        'path' => 'assets/images/' . $filename,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
