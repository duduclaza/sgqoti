<?php
// Novo controlador de logo funcional
require_once __DIR__ . '/../config/database.php';

$storageRel = '/assets/images/logo-preview.png';
$storageAbs = __DIR__ . '/../../assets/images/logo-preview.png';

$method = $_SERVER['REQUEST_METHOD'];

// Upload (aceita apenas PNG, máximo 2MB)
if ($method === 'POST') {
    header('Content-Type: application/json');
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'Arquivo não enviado ou inválido']);
        exit;
    }
    $file = $_FILES['logo'];
    $mime = mime_content_type($file['tmp_name']);
    if ($mime !== 'image/png') {
        echo json_encode(['success' => false, 'error' => 'Apenas PNG é permitido']);
        exit;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Arquivo muito grande (máx 2MB)']);
        exit;
    }
    // Garantir diretório
    if (!is_dir(dirname($storageAbs))) {
        mkdir(dirname($storageAbs), 0755, true);
    }
    if (move_uploaded_file($file['tmp_name'], $storageAbs)) {
        echo json_encode(['success' => true, 'url' => $storageRel]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao salvar logo']);
    }
    exit;
}

// Download direto: retorna o arquivo de imagem
if ($method === 'GET' && isset($_GET['download'])) {
    if (!file_exists($storageAbs)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        exit('Not found');
    }
    $mime = mime_content_type($storageAbs) ?: 'image/png';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($storageAbs));
    readfile($storageAbs);
    exit;
}

// Default: retornar JSON com URL se existir
header('Content-Type: application/json');
if (file_exists($storageAbs)) {
    echo json_encode(['success' => true, 'url' => $storageRel]);
} else {
    echo json_encode(['success' => false, 'error' => 'Logo não encontrada']);
}
