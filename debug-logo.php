<?php
// Debug do sistema de logos
header('Content-Type: text/html; charset=utf-8');

require_once 'backend/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h2>🔍 Debug - Sistema de Logos</h2>";
    
    // Verificar se tabela existe
    $stmt = $conn->query("SHOW TABLES LIKE 'logos'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabela 'logos' existe</p>";
        
        // Listar todos os logos
        $stmt = $conn->query("SELECT * FROM logos ORDER BY created_at DESC");
        $logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>📋 Logos cadastrados (" . count($logos) . "):</h3>";
        
        if (count($logos) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Arquivo</th><th>Tamanho</th><th>Ativo</th><th>Criado</th><th>Teste</th></tr>";
            
            foreach ($logos as $logo) {
                echo "<tr>";
                echo "<td>{$logo['id']}</td>";
                echo "<td>{$logo['nome']}</td>";
                echo "<td>{$logo['tipo']}</td>";
                echo "<td>{$logo['arquivo_nome']}</td>";
                echo "<td>" . number_format($logo['tamanho'] / 1024, 1) . " KB</td>";
                echo "<td>" . ($logo['ativo'] ? 'SIM' : 'NÃO') . "</td>";
                echo "<td>{$logo['created_at']}</td>";
                echo "<td><a href='backend/api/logo-manager.php?download=1' target='_blank'>Ver</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Testar URLs específicas
            echo "<h3>🧪 Testes de URL:</h3>";
            $tipos = ['sidebar', 'header', 'login'];
            foreach ($tipos as $tipo) {
                $url = "backend/api/logo-manager.php?download=1";
                echo "<p><strong>$tipo:</strong> <a href='$url' target='_blank'>$url</a></p>";
            }
            
        } else {
            echo "<p>❌ Nenhum logo encontrado</p>";
        }
        
    } else {
        echo "<p>❌ Tabela 'logos' não existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>

<h3>🔧 Teste JavaScript (Menu Lateral)</h3>
<script>
// Simular o que acontece no index.php
const logoImg = document.createElement('img');
logoImg.src = 'backend/api/logos.php?tipo=sidebar&download=1';
logoImg.style.maxWidth = '200px';
logoImg.style.border = '1px solid #ccc';

logoImg.onload = function() {
    console.log('✅ Logo carregado com sucesso');
    document.body.appendChild(logoImg);
};

logoImg.onerror = function() {
    console.log('❌ Erro ao carregar logo');
    const error = document.createElement('p');
    error.textContent = '❌ Erro ao carregar logo do sidebar';
    error.style.color = 'red';
    document.body.appendChild(error);
};
</script>
