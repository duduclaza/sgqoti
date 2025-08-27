<?php
// Debug completo das chamadas da API em produção
echo "<h2>🔍 Debug Completo - API Calls</h2>";

// Mostrar todas as variáveis de entrada
echo "<h3>1. Dados da Requisição:</h3>";
echo "<strong>Método:</strong> " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "<strong>Content-Type:</strong> " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido') . "<br>";
echo "<strong>User-Agent:</strong> " . ($_SERVER['HTTP_USER_AGENT'] ?? 'não definido') . "<br>";

// Mostrar dados POST
if (!empty($_POST)) {
    echo "<h4>$_POST dados:</h4>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
} else {
    echo "<p>❌ $_POST está vazio</p>";
}

// Mostrar dados JSON
$raw_input = file_get_contents("php://input");
if (!empty($raw_input)) {
    echo "<h4>Raw Input (php://input):</h4>";
    echo "<pre>" . htmlspecialchars($raw_input) . "</pre>";
    
    $json_data = json_decode($raw_input, true);
    if ($json_data) {
        echo "<h4>JSON Decodificado:</h4>";
        echo "<pre>" . print_r($json_data, true) . "</pre>";
    } else {
        echo "<p>❌ Não é JSON válido</p>";
    }
} else {
    echo "<p>❌ Raw input está vazio</p>";
}

// Testar conexão com banco
echo "<h3>2. Teste de Conexão com Banco:</h3>";
try {
    require_once 'backend/config/database.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Conexão estabelecida<br>";
        
        // Testar inserção simples
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, usuario, senha_hash, perfil, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $test_result = $stmt->execute([
            'Teste Debug ' . date('H:i:s'),
            'debug@teste.com',
            'debug_' . time(),
            password_hash('123456', PASSWORD_DEFAULT),
            'user',
            'active'
        ]);
        
        if ($test_result) {
            $user_id = $conn->lastInsertId();
            echo "✅ Inserção de teste bem-sucedida - ID: $user_id<br>";
            
            // Deletar o registro de teste
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$user_id]);
            echo "✅ Registro de teste removido<br>";
        } else {
            echo "❌ Falha na inserção de teste<br>";
        }
        
    } else {
        echo "❌ Falha na conexão<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Simular chamada da API users.php
echo "<h3>3. Simulação da API users.php:</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>📡 Requisição POST recebida - processando...</p>";
    
    // Incluir e executar a API
    ob_start();
    try {
        include 'backend/api/users.php';
        $api_output = ob_get_clean();
        echo "<h4>Saída da API:</h4>";
        echo "<pre>" . htmlspecialchars($api_output) . "</pre>";
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p>❌ Erro ao executar API: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ℹ️ Para testar a API, faça uma requisição POST</p>";
}

// Formulário de teste
echo "<h3>4. Teste Manual:</h3>";
?>

<form method="POST" style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
    <h4>Teste de Cadastro:</h4>
    <input type="hidden" name="action" value="create_user">
    
    <label>Nome:</label><br>
    <input type="text" name="name" value="Teste Manual" required><br><br>
    
    <label>Email:</label><br>
    <input type="email" name="email" value="teste.manual@exemplo.com" required><br><br>
    
    <label>Usuário:</label><br>
    <input type="text" name="username" value="teste_manual" required><br><br>
    
    <label>Senha:</label><br>
    <input type="password" name="password" value="123456" required><br><br>
    
    <label>Perfil:</label><br>
    <select name="role" required>
        <option value="user">Usuário</option>
        <option value="admin">Admin</option>
    </select><br><br>
    
    <label>Status:</label><br>
    <select name="status" required>
        <option value="active">Ativo</option>
        <option value="inactive">Inativo</option>
    </select><br><br>
    
    <button type="submit">🧪 Testar Cadastro</button>
</form>

<script>
// Teste via JavaScript também
function testJS() {
    const data = {
        action: 'create_user',
        name: 'Teste JS',
        email: 'teste.js@exemplo.com',
        username: 'teste_js',
        password: '123456',
        role: 'user',
        status: 'active'
    };
    
    fetch('backend/api/users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(result => {
        document.getElementById('js-result').innerHTML = '<pre>' + result + '</pre>';
    })
    .catch(error => {
        document.getElementById('js-result').innerHTML = '<p style="color: red;">Erro: ' + error.message + '</p>';
    });
}
</script>

<button onclick="testJS()">🚀 Testar via JavaScript</button>
<div id="js-result" style="border: 1px solid #ddd; padding: 10px; margin: 10px 0; min-height: 50px;"></div>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3, h4 { color: #333; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
button { background: #007cba; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
button:hover { background: #005a87; }
input, select { width: 200px; padding: 5px; margin: 2px 0; }
</style>
