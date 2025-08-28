<?php
require_once '../config/database.php';
require_once '../config/cors.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Tabela de filiais
    $pdo->exec("CREATE TABLE IF NOT EXISTS branches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT id, nome, created_at, updated_at FROM branches ORDER BY nome ASC');
            echo json_encode(['success'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { throw new Exception('Payload inválido'); }
            $nome = trim($data['nome'] ?? '');
            if ($nome === '') { throw new Exception('Nome é obrigatório'); }
            $stmt = $pdo->prepare('INSERT INTO branches (nome) VALUES (?)');
            $stmt->execute([$nome]);
            echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
            break;
        case 'PUT':
            parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
            $id = isset($qs['id']) ? (int)$qs['id'] : 0;
            if (!$id) { throw new Exception('ID obrigatório'); }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { throw new Exception('Payload inválido'); }
            $nome = trim($data['nome'] ?? '');
            if ($nome === '') { throw new Exception('Nome é obrigatório'); }
            $stmt = $pdo->prepare('UPDATE branches SET nome=? WHERE id=?');
            $stmt->execute([$nome, $id]);
            echo json_encode(['success'=>true]);
            break;
        case 'DELETE':
            parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
            $id = isset($qs['id']) ? (int)$qs['id'] : 0;
            if (!$id) { throw new Exception('ID obrigatório'); }
            $stmt = $pdo->prepare('DELETE FROM branches WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success'=>true]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error'=>$e->getMessage()]);
}
