<?php
// Teste direto da API de usuários
require_once 'backend/config/cors.php';
require_once 'backend/config/database.php';

echo "<h2>Teste da API de Usuários</h2>";

// Teste 1: Conexão com banco
echo "<h3>1. Testando conexão com banco:</h3>";
try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "✅ Conexão estabelecida<br>";
    
    // Verificar se tabela usuarios existe
    $stmt = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'usuarios' existe<br>";
        
        // Mostrar estrutura da tabela
        $stmt = $conn->query("DESCRIBE usuarios");
        echo "<strong>Estrutura da tabela:</strong><br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    } else {
        echo "❌ Tabela 'usuarios' não existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Teste 2: Listar usuários existentes
echo "<h3>2. Usuários existentes:</h3>";
try {
    $stmt = $conn->query("SELECT * FROM usuarios ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Usuário</th><th>Perfil</th><th>Status</th><th>Criado em</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['nome']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['usuario']}</td>";
            echo "<td>{$user['perfil']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total: " . count($users) . " usuários</strong></p>";
    } else {
        echo "Nenhum usuário encontrado.<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao listar usuários: " . $e->getMessage() . "<br>";
}

// Teste 3: Criar usuário de teste
echo "<h3>3. Testando criação de usuário:</h3>";
$testUser = [
    'name' => 'Teste Usuario',
    'email' => 'teste@exemplo.com',
    'username' => 'teste_user',
    'password' => 'senha123',
    'role' => 'user',
    'status' => 'active'
];

try {
    // Verificar se já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR usuario = ?");
    $stmt->execute([$testUser['email'], $testUser['username']]);
    
    if ($stmt->fetch()) {
        echo "⚠️ Usuário de teste já existe<br>";
    } else {
        // Criar usuário
        $passwordHash = password_hash($testUser['password'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, usuario, senha_hash, perfil, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $testUser['name'],
            $testUser['email'],
            $testUser['username'],
            $passwordHash,
            $testUser['role'],
            $testUser['status']
        ]);
        
        if ($result) {
            $userId = $conn->lastInsertId();
            echo "✅ Usuário de teste criado com ID: $userId<br>";
        } else {
            echo "❌ Erro ao criar usuário de teste<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro no teste de criação: " . $e->getMessage() . "<br>";
}

// Teste 4: Testar API via POST
echo "<h3>4. Testando API users.php:</h3>";
echo "<p><strong>URL da API:</strong> " . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/backend/api/users.php</p>";

// Mostrar exemplo de requisição
echo "<h4>Exemplo de requisição POST:</h4>";
echo "<pre>";
echo "URL: backend/api/users.php\n";
echo "Method: POST\n";
echo "Headers: Content-Type: application/json\n";
echo "Body: " . json_encode([
    'action' => 'create_user',
    'name' => 'Nome Completo',
    'email' => 'email@exemplo.com',
    'username' => 'usuario123',
    'password' => 'senha123',
    'role' => 'user',
    'status' => 'active'
], JSON_PRETTY_PRINT);
echo "</pre>";

echo "<p><a href='backend/api/users.php' target='_blank'>🔗 Testar API users.php (GET)</a></p>";
?>
