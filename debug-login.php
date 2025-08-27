<?php
// Debug script para testar login
require_once 'backend/config/database.php';

echo "<h2>Debug do Sistema de Login</h2>";

// Testar conexão
try {
    $database = new Database();
    $pdo = $database->getConnection();
    echo "<p>✅ Conexão com banco: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ Erro de conexão: " . $e->getMessage() . "</p>";
    exit;
}

// Listar usuários
try {
    $stmt = $pdo->query("SELECT id, nome, email, usuario, senha FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Usuários no banco (" . count($usuarios) . "):</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Usuário</th><th>Senha Hash</th></tr>";
    
    foreach($usuarios as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['usuario']) . "</td>";
        echo "<td>" . substr($user['senha'], 0, 20) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar usuários: " . $e->getMessage() . "</p>";
}

// Testar senha específica
if (!empty($usuarios)) {
    $primeiro_usuario = $usuarios[0];
    echo "<h3>Teste de Senha para: " . htmlspecialchars($primeiro_usuario['email']) . "</h3>";
    
    $senhas_teste = ['123456', 'admin', 'senha', 'password', '123', 'test'];
    
    foreach($senhas_teste as $senha_teste) {
        $valida = password_verify($senha_teste, $primeiro_usuario['senha']);
        echo "<p>Senha '$senha_teste': " . ($valida ? "✅ VÁLIDA" : "❌ Inválida") . "</p>";
    }
}

// Criar usuário de teste
echo "<h3>Criar Usuário de Teste</h3>";
echo "<form method='POST'>";
echo "<p>Email: <input type='email' name='test_email' value='teste@sgq.com' required></p>";
echo "<p>Senha: <input type='password' name='test_senha' value='123456' required></p>";
echo "<p><button type='submit' name='criar_teste'>Criar Usuário Teste</button></p>";
echo "</form>";

if (isset($_POST['criar_teste'])) {
    $email_teste = $_POST['test_email'];
    $senha_teste = $_POST['test_senha'];
    $senha_hash = password_hash($senha_teste, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, usuario, senha) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Usuário Teste', $email_teste, 'teste', $senha_hash]);
        echo "<p>✅ Usuário teste criado com sucesso!</p>";
        echo "<p><strong>Email:</strong> $email_teste</p>";
        echo "<p><strong>Senha:</strong> $senha_teste</p>";
    } catch (Exception $e) {
        echo "<p>❌ Erro ao criar usuário: " . $e->getMessage() . "</p>";
    }
}
?>
