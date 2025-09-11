<?php
// Teste básico para identificar o problema
echo "Teste básico funcionando!<br>";

// Testar sessão
session_start();
echo "Sessão iniciada<br>";

// Testar include do environment
try {
    require_once '../../config/environment.php';
    echo "Environment carregado<br>";
    
    $env = Environment::getInstance();
    echo "Environment instanciado<br>";
    
    echo "Base URL: " . $env->getBaseUrl() . "<br>";
    echo "Assets URL: " . $env->getAssetsUrl() . "<br>";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
}

echo "Teste concluído!";
?>
