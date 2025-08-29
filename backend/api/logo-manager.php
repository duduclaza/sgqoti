<?php
// Novo controlador de logo funcional
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Upload de logo PNG
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
        $target = __DIR__ . '/../../assets/images/logo-preview.png';
        if (move_uploaded_file($file['tmp_name'], $target)) {
            echo json_encode(['success' => true, 'url' => '/assets/images/logo-preview.png']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha ao salvar logo']);
        }
        break;
    case 'GET':
        // Retornar URL do logo
        $logoPath = '/assets/images/logo-preview.png';
        if (file_exists(__DIR__ . '/../../assets/images/logo-preview.png')) {
            echo json_encode(['success' => true, 'url' => $logoPath]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Logo não encontrada']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Método não suportado']);
}
