<?php
require_once 'backend/config/database.php';

echo "<h2>🔍 Teste de Credenciais SGQ</h2>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    echo "<p>✅ Conexão com banco: OK</p>";
    
    // Listar todos os usuários
    echo "<h3>👥 Usuários cadastrados:</h3>";
    $stmt = $pdo->query("SELECT id, nome, email, usuario, created_at FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($usuarios)) {
        echo "<p>❌ Nenhum usuário encontrado!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Usuário</th><th>Criado em</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['nome']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['usuario']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Testar credenciais específicas
    echo "<h3>🔑 Teste de Credenciais Master:</h3>";
    
    $credenciais_teste = [
        ['email' => 'admin@sgqoti.com', 'senha' => 'Admin@123'],
        ['email' => 'admin', 'senha' => 'Admin@123'],
    ];
    
    foreach ($credenciais_teste as $cred) {
        echo "<h4>Testando: {$cred['email']} / {$cred['senha']}</h4>";
        
        // Buscar usuário
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
        $stmt->execute([$cred['email'], $cred['email']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<pre>DEBUG - Campos do usuário: " . print_r($usuario, true) . "</pre>";
        
        if ($usuario) {
            echo "<p>✅ Usuário encontrado: {$usuario['nome']} (ID: {$usuario['id']})</p>";
            
            // Testar senha
            $senha_valida = password_verify($cred['senha'], $usuario['senha']);
            if ($senha_valida) {
                echo "<p style='color: green;'>✅ SENHA CORRETA! Login funcionará.</p>";
            } else {
                echo "<p style='color: red;'>❌ SENHA INCORRETA!</p>";
                echo "<p>Hash armazenado: " . substr($usuario['senha'], 0, 50) . "...</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Usuário não encontrado!</p>";
        }
        echo "<hr>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
