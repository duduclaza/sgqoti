<?php
// Debug para verificar se arquivos existem em produção
echo "<h2>Debug - Verificação de Arquivos em Produção</h2>";

// Verificar se arquivos backend existem
$files_to_check = [
    'backend/config/database.php',
    'backend/config/cors.php', 
    'backend/api/users.php',
    'backend/api/config/database-config.php'
];

echo "<h3>1. Verificando arquivos backend:</h3>";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
        
        // Verificar se é legível
        if (is_readable($file)) {
            echo "&nbsp;&nbsp;&nbsp;📖 Arquivo legível<br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;❌ Arquivo não legível<br>";
        }
        
        // Mostrar tamanho
        $size = filesize($file);
        echo "&nbsp;&nbsp;&nbsp;📏 Tamanho: $size bytes<br>";
        
    } else {
        echo "❌ $file NÃO EXISTE<br>";
    }
}

// Verificar permissões da pasta backend
echo "<h3>2. Verificando permissões:</h3>";
if (is_dir('backend')) {
    echo "✅ Pasta backend/ existe<br>";
    if (is_readable('backend')) {
        echo "✅ Pasta backend/ é legível<br>";
    } else {
        echo "❌ Pasta backend/ não é legível<br>";
    }
} else {
    echo "❌ Pasta backend/ NÃO EXISTE<br>";
}

// Testar inclusão do arquivo users.php
echo "<h3>3. Testando inclusão de users.php:</h3>";
try {
    if (file_exists('backend/api/users.php')) {
        // Capturar output
        ob_start();
        include 'backend/api/users.php';
        $output = ob_get_clean();
        
        echo "✅ Arquivo users.php incluído com sucesso<br>";
        if (!empty($output)) {
            echo "📄 Output capturado: " . htmlspecialchars(substr($output, 0, 200)) . "...<br>";
        }
    } else {
        echo "❌ Arquivo users.php não encontrado<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao incluir users.php: " . $e->getMessage() . "<br>";
}

// Verificar configuração PHP
echo "<h3>4. Configuração PHP:</h3>";
echo "📋 Versão PHP: " . phpversion() . "<br>";
echo "📋 Display Errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "📋 Error Reporting: " . error_reporting() . "<br>";

// Verificar extensões necessárias
$extensions = ['pdo', 'pdo_mysql', 'json'];
echo "<h3>5. Extensões PHP:</h3>";
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext carregada<br>";
    } else {
        echo "❌ $ext NÃO carregada<br>";
    }
}

// Testar conexão direta com banco
echo "<h3>6. Teste de conexão com banco:</h3>";
try {
    if (file_exists('backend/config/database.php')) {
        require_once 'backend/config/database.php';
        $db = new Database();
        $conn = $db->getConnection();
        echo "✅ Conexão com banco estabelecida<br>";
        
        // Testar query simples
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "✅ Query de teste executada: " . $result['test'] . "<br>";
        
    } else {
        echo "❌ Arquivo database.php não encontrado<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Mostrar informações do servidor
echo "<h3>7. Informações do Servidor:</h3>";
echo "🌐 HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'não definido') . "<br>";
echo "🌐 SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'não definido') . "<br>";
echo "📁 DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'não definido') . "<br>";
echo "📁 SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'não definido') . "<br>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; border-bottom: 2px solid #007cba; }
h3 { color: #007cba; margin-top: 20px; }
</style>
