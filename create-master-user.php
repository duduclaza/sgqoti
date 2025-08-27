<?php
// Script para criar usuário master
require_once 'backend/config/database.php';

echo "<h2>Criar Usuário Master</h2>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    echo "<p>✅ Conexão com banco: OK</p>";
    
    // Dados do usuário master
    $nome = 'Administrador Master';
    $email = 'admin@sgqoti.com';
    $usuario = 'admin';
    $senha = 'Admin@123';
    
    // Verificar se já existe
    $stmt_check = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
    $stmt_check->execute([$email, $usuario]);
    $existe = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($existe) {
        echo "<p>⚠️ Usuário master já existe. Atualizando senha...</p>";
        
        // Atualizar senha do usuário existente
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt_update = $pdo->prepare("UPDATE usuarios SET senha = ?, nome = ? WHERE email = ? OR usuario = ?");
        $resultado = $stmt_update->execute([$senha_hash, $nome, $email, $usuario]);
        
        if ($resultado) {
            echo "<p>✅ Senha do usuário master atualizada!</p>";
        } else {
            echo "<p>❌ Erro ao atualizar usuário master</p>";
        }
        
    } else {
        echo "<p>📝 Criando novo usuário master...</p>";
        
        // Criar novo usuário master
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt_create = $pdo->prepare("INSERT INTO usuarios (nome, email, usuario, senha, created_at) VALUES (?, ?, ?, ?, NOW())");
        $resultado = $stmt_create->execute([$nome, $email, $usuario, $senha_hash]);
        
        if ($resultado) {
            echo "<p>✅ Usuário master criado com sucesso!</p>";
        } else {
            echo "<p>❌ Erro ao criar usuário master</p>";
        }
    }
    
    // Mostrar credenciais
    echo "<div style='background: #e3f2fd; padding: 20px; border: 2px solid #2196f3; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🔑 Credenciais do Usuário Master</h3>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr><td style='padding: 8px; font-weight: bold;'>Nome:</td><td style='padding: 8px;'>$nome</td></tr>";
    echo "<tr><td style='padding: 8px; font-weight: bold;'>Email:</td><td style='padding: 8px;'>$email</td></tr>";
    echo "<tr><td style='padding: 8px; font-weight: bold;'>Usuário:</td><td style='padding: 8px;'>$usuario</td></tr>";
    echo "<tr><td style='padding: 8px; font-weight: bold;'>Senha:</td><td style='padding: 8px; color: #d32f2f; font-weight: bold;'>$senha</td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Testar a senha
    $senha_hash_final = password_hash($senha, PASSWORD_DEFAULT);
    $teste_senha = password_verify($senha, $senha_hash_final);
    echo "<p>✅ Teste da senha: " . ($teste_senha ? "VÁLIDA" : "ERRO") . "</p>";
    
    // Listar todos os usuários
    echo "<h3>👥 Usuários no Sistema</h3>";
    $stmt_all = $pdo->query("SELECT id, nome, email, usuario, created_at FROM usuarios ORDER BY created_at DESC");
    $todos_usuarios = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f5f5f5;'><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Nome</th><th style='padding: 8px;'>Email</th><th style='padding: 8px;'>Usuário</th><th style='padding: 8px;'>Criado em</th></tr>";
    foreach($todos_usuarios as $user) {
        $highlight = ($user['email'] == $email) ? "style='background: #e8f5e8;'" : "";
        echo "<tr $highlight>";
        echo "<td style='padding: 8px;'>" . $user['id'] . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($user['nome']) . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td style='padding: 8px;'>" . htmlspecialchars($user['usuario']) . "</td>";
        echo "<td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($user['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='index.php' style='background: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🏠 Ir para Login</a>";
echo "<a href='debug-login.php' style='background: #ff9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>🔧 Debug Login</a>";
echo "</div>";
?>
