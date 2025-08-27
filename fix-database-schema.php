<?php
require_once 'backend/config/database.php';

echo "<h2>🔧 Correção da Estrutura da Tabela Usuarios</h2>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    echo "<p>✅ Conexão com banco: OK</p>";
    
    // Verificar estrutura atual da tabela
    echo "<h3>📋 Estrutura atual da tabela usuarios:</h3>";
    $stmt = $pdo->query("DESCRIBE usuarios");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($campos as $campo) {
        echo "<tr>";
        echo "<td>{$campo['Field']}</td>";
        echo "<td>{$campo['Type']}</td>";
        echo "<td>{$campo['Null']}</td>";
        echo "<td>{$campo['Key']}</td>";
        echo "<td>{$campo['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar se campo senha existe
    $tem_senha = false;
    foreach ($campos as $campo) {
        if ($campo['Field'] === 'senha') {
            $tem_senha = true;
            break;
        }
    }
    
    if (!$tem_senha) {
        echo "<h3>⚠️ Campo 'senha' não existe! Adicionando...</h3>";
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN senha VARCHAR(255) NOT NULL DEFAULT ''");
        echo "<p>✅ Campo 'senha' adicionado com sucesso!</p>";
    } else {
        echo "<h3>✅ Campo 'senha' já existe!</h3>";
    }
    
    // Atualizar senha do usuário admaster
    echo "<h3>🔑 Atualizando senha do usuário admaster:</h3>";
    $senha_hash = password_hash('Admin@123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@sgqoti.com'");
    $resultado = $stmt->execute([$senha_hash]);
    
    if ($resultado) {
        echo "<p>✅ Senha do usuário admaster atualizada!</p>";
        echo "<p><strong>Email:</strong> admin@sgqoti.com</p>";
        echo "<p><strong>Senha:</strong> Admin@123</p>";
    } else {
        echo "<p>❌ Erro ao atualizar senha!</p>";
    }
    
    // Testar a senha atualizada
    echo "<h3>🧪 Teste da senha atualizada:</h3>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = 'admin@sgqoti.com'");
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify('Admin@123', $usuario['senha'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ SUCESSO! Login funcionará agora!</p>";
    } else {
        echo "<p style='color: red;'>❌ Ainda há problema com a senha!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
