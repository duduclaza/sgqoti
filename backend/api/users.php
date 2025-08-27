<?php
require_once '../config/cors.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Tentar ler dados JSON primeiro
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);

    // Se não houver JSON válido, usar $_POST como fallback
    if (!$input && !empty($_POST)) {
        $input = $_POST;
    }

    // Se ainda não houver dados, retornar erro
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Dados inválidos'
        ]);
        exit;
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'create_user':
            createUser($input);
            break;
        case 'list_users':
            listUsers();
            break;
        case 'update_user':
            updateUser($input);
            break;
        case 'delete_user':
            deleteUser($input);
            break;
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ação não reconhecida: ' . $action
            ]);
            break;
    }
} else if ($method === 'GET') {
    // Listar usuários
    listUsers();
} else {
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
                throw new Exception("Campo obrigatório: $field");
            }
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Email já cadastrado");
        }
        
        // Verificar se usuário já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
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
            throw new Exception("Erro ao inserir usuário no banco de dados");
        }
        
    } catch (Exception $e) {
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
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuários carregados com sucesso',
            'data' => $users,
            'count' => count($users),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
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
            throw new Exception("Nenhum campo para atualizar");
        }
        
        $fields[] = "updated_at = NOW()";
        $values[] = $data['id'];
        
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute($values)) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            throw new Exception("Erro ao atualizar usuário");
        }
        
    } catch (Exception $e) {
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
            throw new Exception("ID do usuário é obrigatório");
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        
        if ($stmt->execute([$data['id']])) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuário excluído com sucesso',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            throw new Exception("Erro ao excluir usuário");
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao excluir usuário: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
?>
