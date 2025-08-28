<?php
// Teste da API de Logos
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste - API de Logos SGQ OTI</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico - Sistema de Logos SGQ OTI</h1>
    
    <?php
    // Teste 1: Verificar se o arquivo da API existe
    echo "<div class='test-section'>";
    echo "<h3>📁 Teste 1: Verificação de Arquivos</h3>";
    
    $apiFile = 'backend/api/logos.php';
    if (file_exists($apiFile)) {
        echo "<div class='success'>✅ API de logos encontrada: $apiFile</div>";
    } else {
        echo "<div class='error'>❌ API de logos não encontrada: $apiFile</div>";
    }
    
    $configFile = 'backend/config/database.php';
    if (file_exists($configFile)) {
        echo "<div class='success'>✅ Configuração de banco encontrada: $configFile</div>";
    } else {
        echo "<div class='error'>❌ Configuração de banco não encontrada: $configFile</div>";
    }
    echo "</div>";
    
    // Teste 2: Testar conexão com banco
    echo "<div class='test-section'>";
    echo "<h3>🔌 Teste 2: Conexão com Banco de Dados</h3>";
    
    try {
        require_once 'backend/config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        echo "<div class='success'>✅ Conexão com banco estabelecida</div>";
        
        // Teste 3: Verificar se tabela existe
        echo "<h3>📋 Teste 3: Verificação da Tabela 'logos'</h3>";
        
        $stmt = $conn->query("SHOW TABLES LIKE 'logos'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✅ Tabela 'logos' existe</div>";
            
            // Verificar estrutura da tabela
            $stmt = $conn->query("DESCRIBE logos");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<div class='info'><strong>Estrutura da tabela:</strong><br>";
            foreach ($columns as $col) {
                echo "- {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Key']}<br>";
            }
            echo "</div>";
            
            // Contar registros
            $stmt = $conn->query("SELECT COUNT(*) as total FROM logos");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='info'>📊 Total de logos cadastrados: {$count['total']}</div>";
            
        } else {
            echo "<div class='error'>❌ Tabela 'logos' não existe</div>";
            echo "<div class='info'>🔧 Tentando criar tabela...</div>";
            
            // Tentar criar tabela
            $createTable = "
                CREATE TABLE IF NOT EXISTS logos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    tipo ENUM('sidebar', 'header', 'login') NOT NULL,
                    arquivo_nome VARCHAR(255) NOT NULL,
                    mime_type VARCHAR(100) NOT NULL,
                    tamanho INT NOT NULL,
                    dados LONGBLOB NOT NULL,
                    ativo BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_tipo_ativo (tipo, ativo)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            try {
                $conn->exec($createTable);
                echo "<div class='success'>✅ Tabela 'logos' criada com sucesso</div>";
            } catch (Exception $e) {
                echo "<div class='error'>❌ Erro ao criar tabela: " . $e->getMessage() . "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Erro na conexão: " . $e->getMessage() . "</div>";
    }
    echo "</div>";
    
    // Teste 4: Testar API via cURL
    echo "<div class='test-section'>";
    echo "<h3>🌐 Teste 4: Teste da API via HTTP</h3>";
    
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/backend/api/logos.php';
    
    // Teste GET
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<div class='error'>❌ Erro cURL: $error</div>";
    } else {
        echo "<div class='info'>🔗 URL testada: $apiUrl</div>";
        echo "<div class='info'>📡 Código HTTP: $httpCode</div>";
        
        if ($httpCode == 200) {
            echo "<div class='success'>✅ API respondeu com sucesso</div>";
            echo "<div class='info'><strong>Resposta:</strong><pre>" . htmlspecialchars($response) . "</pre></div>";
        } else {
            echo "<div class='error'>❌ API retornou código de erro: $httpCode</div>";
            echo "<div class='info'><strong>Resposta:</strong><pre>" . htmlspecialchars($response) . "</pre></div>";
        }
    }
    echo "</div>";
    
    // Teste 5: Verificar permissões de upload
    echo "<div class='test-section'>";
    echo "<h3>📤 Teste 5: Configurações de Upload</h3>";
    
    $uploadMaxSize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    $maxExecutionTime = ini_get('max_execution_time');
    $memoryLimit = ini_get('memory_limit');
    
    echo "<div class='info'>";
    echo "📊 <strong>Configurações PHP:</strong><br>";
    echo "- upload_max_filesize: $uploadMaxSize<br>";
    echo "- post_max_size: $postMaxSize<br>";
    echo "- max_execution_time: $maxExecutionTime segundos<br>";
    echo "- memory_limit: $memoryLimit<br>";
    echo "</div>";
    
    // Verificar se extensões necessárias estão carregadas
    $extensions = ['gd', 'fileinfo'];
    foreach ($extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<div class='success'>✅ Extensão '$ext' carregada</div>";
        } else {
            echo "<div class='error'>❌ Extensão '$ext' não encontrada</div>";
        }
    }
    echo "</div>";
    ?>
    
    <div class="test-section">
        <h3>🧪 Teste 6: Interface de Upload</h3>
        <form id="testUploadForm" enctype="multipart/form-data" style="border: 2px dashed #ccc; padding: 20px; border-radius: 8px;">
            <div style="margin-bottom: 15px;">
                <label><strong>Tipo:</strong></label><br>
                <select name="tipo" style="padding: 8px; margin-top: 5px;">
                    <option value="sidebar">Sidebar</option>
                    <option value="header">Header</option>
                    <option value="login">Login</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Nome:</strong></label><br>
                <input type="text" name="nome" value="Logo Teste" style="padding: 8px; margin-top: 5px; width: 200px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Arquivo:</strong></label><br>
                <input type="file" name="logo" accept="image/*" style="margin-top: 5px;">
            </div>
            <button type="submit" style="background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                📤 Testar Upload
            </button>
        </form>
        <div id="uploadResult" style="margin-top: 15px;"></div>
    </div>
    
    <script>
    document.getElementById('testUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('uploadResult');
        
        resultDiv.innerHTML = '<div style="color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px;">⏳ Enviando arquivo...</div>';
        
        fetch('backend/api/logos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    resultDiv.innerHTML = '<div style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;">✅ Upload realizado com sucesso!<br><strong>ID:</strong> ' + data.logo_id + '<br><strong>URL:</strong> ' + data.url + '</div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ Erro: ' + data.error + '</div>';
                }
            } catch (e) {
                resultDiv.innerHTML = '<div style="color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ Resposta inválida do servidor:<br><pre>' + text + '</pre></div>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<div style="color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ Erro na requisição: ' + error.message + '</div>';
        });
    });
    </script>
    
    <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-radius: 8px;">
        <h3>📋 Resumo do Diagnóstico</h3>
        <p><strong>Para resolver problemas com logos:</strong></p>
        <ol>
            <li>Verifique se todos os testes acima passaram ✅</li>
            <li>Se a tabela não existir, ela será criada automaticamente</li>
            <li>Teste o upload usando o formulário acima</li>
            <li>Verifique as configurações PHP se houver problemas de upload</li>
            <li>Consulte os logs do servidor para erros detalhados</li>
        </ol>
        
        <p><strong>Acesso direto à API:</strong></p>
        <ul>
            <li>Listar logos: <code><?= $apiUrl ?></code></li>
            <li>Buscar por tipo: <code><?= $apiUrl ?>?tipo=sidebar</code></li>
            <li>Download: <code><?= $apiUrl ?>?tipo=sidebar&download=1</code></li>
        </ul>
    </div>
    
</body>
</html>
