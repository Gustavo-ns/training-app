<?php
/**
 * Configuración para el sistema de almacenamiento de datos
 */

// Configuración de rutas
define('BASE_PATH', dirname(__FILE__));
define('DATA_PATH', BASE_PATH . '/../data/users/');

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('BACKUP_RETENTION_DAYS', 30);
define('MAX_HISTORY_ENTRIES', 365); // Un año de historial

// Configuración de la aplicación
define('APP_VERSION', '1.0.0');
define('API_VERSION', '1.0');

// Configuración de seguridad
define('ALLOWED_ORIGINS', ['http://localhost', 'http://127.0.0.1']);
define('MAX_REQUESTS_PER_MINUTE', 60);

/**
 * Función para obtener la configuración completa
 */
function getConfig() {
    return [
        'paths' => [
            'base' => BASE_PATH,
            'data' => DATA_PATH
        ],
        'limits' => [
            'maxFileSize' => MAX_FILE_SIZE,
            'backupRetentionDays' => BACKUP_RETENTION_DAYS,
            'maxHistoryEntries' => MAX_HISTORY_ENTRIES,
            'maxRequestsPerMinute' => MAX_REQUESTS_PER_MINUTE
        ],
        'app' => [
            'version' => APP_VERSION,
            'apiVersion' => API_VERSION
        ],
        'security' => [
            'allowedOrigins' => ALLOWED_ORIGINS
        ]
    ];
}

/**
 * Función para logging de errores
 */
function logError($message, $data = null) {
    $logFile = BASE_PATH . '/../data/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message";
    if ($data) {
        $logEntry .= " - Data: " . json_encode($data);
    }
    $logEntry .= "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Función para logging de actividad
 */
function logActivity($userId, $action, $message) {
    $logFile = BASE_PATH . '/../data/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] User: $userId | Action: $action | $message\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>