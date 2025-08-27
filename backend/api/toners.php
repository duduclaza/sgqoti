<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$pdo = $database->getConnection();

// Criar tabela se não existir
createTonerTable($pdo);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                getToner($pdo, $_GET['id']);
            } else {
                getAllToners($pdo);
            }
            break;
            
        case 'POST':
            createToner($pdo);
            break;
            
        case 'PUT':
            updateToner($pdo, $_GET['id']);
            break;
            
        case 'DELETE':
            deleteToner($pdo, $_GET['id']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function createTonerTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS toners (
        id INT PRIMARY KEY AUTO_INCREMENT,
        modelo VARCHAR(100) NOT NULL,
        peso_cheio DECIMAL(8,2) NOT NULL,
        peso_vazio DECIMAL(8,2) NOT NULL,
        gramatura DECIMAL(8,2) GENERATED ALWAYS AS (peso_cheio - peso_vazio) STORED,
        capacidade INT NOT NULL,
        gramatura_folha DECIMAL(10,6) GENERATED ALWAYS AS ((peso_cheio - peso_vazio) / capacidade) STORED,
        preco DECIMAL(10,2) NOT NULL,
        preco_folha DECIMAL(10,6) GENERATED ALWAYS AS (preco / capacidade) STORED,
        cor ENUM('Black', 'Cyan', 'Magenta', 'Yellow') NOT NULL,
        tipo ENUM('Compativel', 'Original', 'Remanufaturado') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
}

function getAllToners($pdo) {
    $stmt = $pdo->query("SELECT * FROM toners ORDER BY created_at DESC");
    $toners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $toners,
        'count' => count($toners)
    ]);
}

function getToner($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    $toner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($toner) {
        echo json_encode(['success' => true, 'data' => $toner]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Toner não encontrado']);
    }
}

function createToner($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados JSON inválidos');
    }
    
    // Validar campos obrigatórios
    $required = ['modelo', 'peso_cheio', 'peso_vazio', 'capacidade', 'preco', 'cor', 'tipo'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Campo obrigatório: $field");
        }
    }
    
    // Validar valores numéricos
    if ($input['peso_cheio'] <= $input['peso_vazio']) {
        throw new Exception('Peso cheio deve ser maior que peso vazio');
    }
    
    if ($input['capacidade'] <= 0) {
        throw new Exception('Capacidade deve ser maior que zero');
    }
    
    if ($input['preco'] <= 0) {
        throw new Exception('Preço deve ser maior que zero');
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO toners (modelo, peso_cheio, peso_vazio, capacidade, preco, cor, tipo) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $input['modelo'],
        $input['peso_cheio'],
        $input['peso_vazio'],
        $input['capacidade'],
        $input['preco'],
        $input['cor'],
        $input['tipo']
    ]);
    
    $id = $pdo->lastInsertId();
    
    // Buscar o toner criado com campos calculados
    $stmt = $pdo->prepare("SELECT * FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    $toner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Toner cadastrado com sucesso',
        'data' => $toner
    ]);
}

function updateToner($pdo, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados JSON inválidos');
    }
    
    // Verificar se toner existe
    $stmt = $pdo->prepare("SELECT id FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Toner não encontrado']);
        return;
    }
    
    // Validar valores se fornecidos
    if (isset($input['peso_cheio']) && isset($input['peso_vazio'])) {
        if ($input['peso_cheio'] <= $input['peso_vazio']) {
            throw new Exception('Peso cheio deve ser maior que peso vazio');
        }
    }
    
    if (isset($input['capacidade']) && $input['capacidade'] <= 0) {
        throw new Exception('Capacidade deve ser maior que zero');
    }
    
    if (isset($input['preco']) && $input['preco'] <= 0) {
        throw new Exception('Preço deve ser maior que zero');
    }
    
    $fields = [];
    $values = [];
    
    $allowedFields = ['modelo', 'peso_cheio', 'peso_vazio', 'capacidade', 'preco', 'cor', 'tipo'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $fields[] = "$field = ?";
            $values[] = $input[$field];
        }
    }
    
    if (empty($fields)) {
        throw new Exception('Nenhum campo para atualizar');
    }
    
    $values[] = $id;
    
    $stmt = $pdo->prepare("UPDATE toners SET " . implode(', ', $fields) . " WHERE id = ?");
    $stmt->execute($values);
    
    // Buscar o toner atualizado
    $stmt = $pdo->prepare("SELECT * FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    $toner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Toner atualizado com sucesso',
        'data' => $toner
    ]);
}

function deleteToner($pdo, $id) {
    // Verificar se toner existe
    $stmt = $pdo->prepare("SELECT id FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Toner não encontrado']);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM toners WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Toner excluído com sucesso'
    ]);
}
?>
