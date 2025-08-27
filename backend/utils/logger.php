<?php
class Logger {
    private static $logDir = __DIR__ . '/../logs';
    private static $logFile = __DIR__ . '/../logs/app.log';

    private static function ensureLogPath() {
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0775, true);
        }
        if (!file_exists(self::$logFile)) {
            @touch(self::$logFile);
            @chmod(self::$logFile, 0664);
        }
    }

    public static function log($message, $context = [], $level = 'INFO') {
        try {
            self::ensureLogPath();
            $entry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => strtoupper($level),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
                'message' => $message,
                'context' => $context,
            ];
            $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            @file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Evitar quebrar a aplicação por causa do log
        }
    }
}
