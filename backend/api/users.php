<?php
require_once '../config/cors.php';
require_once '../config/database.php';
require_once '../utils/logger.php';

header('Content-Type: application/json');

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];
Logger::log('users.php request start', [
    'method' => $method,
    'query' => $_GET ?? [],
    'headers' => function_exists('getallheaders') ? (getallheaders() ?: []) : []
]);

if ($method === 'POST') {
    // Tentar ler dados JSON primeiro
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);
    Logger::log('POST payload received', [
        'raw_length' => strlen($raw ?? ''),
        'json_decoded' => is_array($input),
    ]);

    // Se não houver JSON válido, usar $_POST como fallback
    if (!$input && !empty($_POST)) {
        $input = $_POST;
    }

    // Se ainda não houver dados, retornar erro
    if (!$input) {
        Logger::log('POST without input', [], 'WARN');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Dados inválidos'
        ]);
        exit;
    }
    
    $action = $input['action'] ?? '';
    Logger::log('POST action resolved', [ 'action' => $action, 'keys' => array_keys($input) ]);
    
    switch ($action) {
        case 'create_user':
            Logger::log('Action create_user called');
            createUser($input);
            break;
        case 'list_users':
            Logger::log('Action list_users called');
            listUsers();
            break;
        case 'update_user':
            Logger::log('Action update_user called');
            updateUser($input);
            break;
        case 'delete_user':
            Logger::log('Action delete_user called');
            deleteUser($input);
            break;
        default:
            Logger::log('Unknown action', [ 'action' => $action ], 'WARN');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: ' . $action
            ]);
            break;
    }
} else if ($method === 'GET') {
    // Listar usuários
    Logger::log('GET list users');
    listUsers();
} else {
    Logger::log('Method not allowed', [ 'method' => $method ], 'WARN');
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}

function createUser($data) {
    try {
        // Validar dados obrigatórios
        $requiredFields = ['name', 'email', 'username', 'password', 'role', 'status'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                Logger::log('createUser missing field', [ 'field' => $field ], 'WARN');
                throw new Exception("Campo obrigatório: $field");
            }
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            Logger::log('createUser email exists', [ 'email' => $data['email'] ], 'WARN');
            throw new Exception("Email já cadastrado");
        }
        
        // Verificar se usuário já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            Logger::log('createUser username exists', [ 'username' => $data['username'] ], 'WARN');
            throw new Exception("Nome de usuário já existe");
        }
        
        // Hash da senha
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Inserir usuário
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, usuario, senha_hash, perfil, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['username'],
            $passwordHash,
            $data['role'],
            $data['status']
        ]);
        
        if ($result) {
            $userId = $conn->lastInsertId();
            Logger::log('createUser success', [ 'id' => $userId, 'username' => $data['username'] ]);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Usuário cadastrado com sucesso',
                'data' => [
                    'id' => $userId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'role' => $data['role'],
                    'status' => $data['status']
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            Logger::log('createUser DB insert failed', [], 'ERROR');
            throw new Exception("Erro ao inserir usuário no banco de dados");
        }
        
    } catch (Exception $e) {
        Logger::log('createUser exception', [ 'error' => $e->getMessage() ], 'ERROR');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao cadastrar usuário: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function listUsers() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->query("
            SELECT id, nome, email, usuario, perfil, status, ultimo_acesso, created_at 
            FROM usuarios 
            ORDER BY created_at DESC
        ");
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Logger::log('listUsers success', [ 'count' => count($users) ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuários carregados com sucesso',
            'data' => $users,
            'count' => count($users),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        Logger::log('listUsers exception', [ 'error' => $e->getMessage() ], 'ERROR');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao listar usuários: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function updateUser($data) {
    try {
        if (empty($data['id'])) {
            Logger::log('updateUser missing id', [], 'WARN');
            throw new Exception("ID do usuário é obrigatório");
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Construir query dinamicamente baseado nos campos fornecidos
        $fields = [];
        $values = [];
        
        if (!empty($data['name'])) {
            $fields[] = "nome = ?";
            $values[] = $data['name'];
        }
        
        if (!empty($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        
        if (!empty($data['username'])) {
            $fields[] = "usuario = ?";
            $values[] = $data['username'];
        }
        
        if (!empty($data['password'])) {
            $fields[] = "senha_hash = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (!empty($data['role'])) {
            $fields[] = "perfil = ?";
            $values[] = $data['role'];
        }
        
        if (!empty($data['status'])) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }
        
        if (empty($fields)) {
            Logger::log('updateUser no fields to update', [], 'WARN');
            throw new Exception("Nenhum campo para atualizar");
        }
        
        $fields[] = "updated_at = NOW()";
        $values[] = $data['id'];
        
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute($values)) {
            Logger::log('updateUser success', [ 'id' => $data['id'] ]);
            echo json_encode([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            Logger::log('updateUser execute failed', [ 'id' => $data['id'] ], 'ERROR');
            throw new Exception("Erro ao atualizar usuário");
        }
        
    } catch (Exception $e) {
        Logger::log('updateUser exception', [ 'error' => $e->getMessage(), 'id' => $data['id'] ?? null ], 'ERROR');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar usuário: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

function deleteUser($data) {
    try {
        if (empty($data['id'])) {
            Logger::log('deleteUser missing id', [], 'WARN');
            throw new Exception("ID do usuário é obrigatório");
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        
        if ($stmt->execute([$data['id']])) {
            Logger::log('deleteUser success', [ 'id' => $data['id'] ]);
            echo json_encode([
                'success' => true,
                'message' => 'Usuário excluído com sucesso',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            Logger::log('deleteUser execute failed', [ 'id' => $data['id'] ], 'ERROR');
            throw new Exception("Erro ao excluir usuário");
        }
        
    } catch (Exception $e) {
        Logger::log('deleteUser exception', [ 'error' => $e->getMessage(), 'id' => $data['id'] ?? null ], 'ERROR');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao excluir usuário: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
?>
