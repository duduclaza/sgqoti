<?php
/**
 * Configuração de Ambiente - SGQ OTI
 * Detecta automaticamente se está em localhost ou produção
 */

class Environment {
    private static $config = null;
    
    public static function getConfig() {
        if (self::$config === null) {
            self::detectEnvironment();
        }
        return self::$config;
    }
    
    private static function detectEnvironment() {
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                   (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                   (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        
        $protocol = $isHttps ? 'https' : 'http';
        
        // Detectar ambiente baseado no host
        if (strpos($host, 'localhost') !== false || 
            strpos($host, '127.0.0.1') !== false || 
            strpos($host, '::1') !== false ||
            strpos($host, '.local') !== false) {
            
            // Ambiente de desenvolvimento (localhost)
            self::$config = [
                'environment' => 'development',
                'debug' => true,
                'base_url' => $protocol . '://' . $host . '/SGQOTI',
                'api_url' => $protocol . '://' . $host . '/SGQOTI/api',
                'assets_url' => $protocol . '://' . $host . '/SGQOTI/assets',
                'db_config' => [
                    'host' => 'srv1890.hstgr.io',
                    'port' => '3306',
                    'database' => 'u230868210_sgqoti',
                    'username' => 'u230868210_otiplus',
                    'password' => 'Pandora@1989'
                ]
            ];
        } else {
            // Ambiente de produção
            self::$config = [
                'environment' => 'production',
                'debug' => false,
                'base_url' => 'https://sgq.sgqoti.com.br',
                'api_url' => 'https://sgq.sgqoti.com.br/api',
                'assets_url' => 'https://sgq.sgqoti.com.br/assets',
                'db_config' => [
                    'host' => 'srv1890.hstgr.io',
                    'port' => '3306',
                    'database' => 'u230868210_sgqoti',
                    'username' => 'u230868210_otiplus',
                    'password' => 'Pandora@1989'
                ]
            ];
        }
    }
    
    public static function isDevelopment() {
        $config = self::getConfig();
        return $config['environment'] === 'development';
    }
    
    public static function isProduction() {
        $config = self::getConfig();
        return $config['environment'] === 'production';
    }
    
    public static function getBaseUrl() {
        $config = self::getConfig();
        return $config['base_url'];
    }
    
    public static function getApiUrl() {
        $config = self::getConfig();
        return $config['api_url'];
    }
    
    public static function getAssetsUrl() {
        $config = self::getConfig();
        return $config['assets_url'];
    }
    
    public static function getDbConfig() {
        $config = self::getConfig();
        return $config['db_config'];
    }
    
    public static function isDebugEnabled() {
        $config = self::getConfig();
        return $config['debug'];
    }
    
    /**
     * Gera URL relativa baseada no ambiente
     */
    public static function url($path = '') {
        $config = self::getConfig();
        $path = ltrim($path, '/');
        
        if (self::isDevelopment()) {
            return $config['base_url'] . ($path ? '/' . $path : '');
        } else {
            return $config['base_url'] . ($path ? '/' . $path : '');
        }
    }
    
    /**
     * Gera caminho para assets
     */
    public static function asset($path) {
        $config = self::getConfig();
        $path = ltrim($path, '/');
        return $config['assets_url'] . '/' . $path;
    }
    
    /**
     * Gera URL para API
     */
    public static function api($endpoint) {
        $config = self::getConfig();
        $endpoint = ltrim($endpoint, '/');
        return $config['api_url'] . '/' . $endpoint;
    }
}

// Configurar tratamento de erros baseado no ambiente
if (Environment::isProduction()) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>
