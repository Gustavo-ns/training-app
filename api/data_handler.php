<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Configuración
define('DATA_DIR', '../data/users/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB máximo por archivo
define('BACKUP_RETENTION_DAYS', 30); // Mantener backups por 30 días

/**
 * Clase para manejar operaciones de datos JSON
 */
class WorkoutDataHandler {
    
    private $userId;
    private $userDir;
    
    public function __construct($userId) {
        $this->userId = $this->sanitizeUserId($userId);
        $this->userDir = DATA_DIR . $this->userId . '/';
        $this->ensureUserDirectory();
    }
    
    /**
     * Sanitizar ID de usuario
     */
    private function sanitizeUserId($userId) {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', $userId);
    }
    
    /**
     * Crear directorio de usuario si no existe
     */
    private function ensureUserDirectory() {
        if (!is_dir($this->userDir)) {
            mkdir($this->userDir, 0755, true);
        }
        
        // Crear subdirectorios
        $subdirs = ['progress', 'history', 'backups'];
        foreach ($subdirs as $subdir) {
            $path = $this->userDir . $subdir . '/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
    
    /**
     * Guardar progreso de entrenamiento
     */
    public function saveProgress($data) {
        try {
            $filename = $this->userDir . 'progress/current_progress.json';
            
            // Agregar metadatos
            $data['savedAt'] = date('c');
            $data['userId'] = $this->userId;
            
            // Crear backup del progreso anterior
            if (file_exists($filename)) {
                $backupName = $this->userDir . 'backups/progress_' . date('Y-m-d_H-i-s') . '.json';
                copy($filename, $backupName);
            }
            
            // Guardar nuevo progreso
            $result = file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
            
            if ($result !== false) {
                $this->cleanOldBackups();
                return ['success' => true, 'message' => 'Progreso guardado exitosamente', 'file' => $filename];
            } else {
                return ['success' => false, 'message' => 'Error escribiendo archivo'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cargar progreso de entrenamiento
     */
    public function loadProgress() {
        try {
            $filename = $this->userDir . 'progress/current_progress.json';
            
            if (!file_exists($filename)) {
                return ['success' => true, 'data' => null, 'message' => 'No hay progreso guardado'];
            }
            
            $content = file_get_contents($filename);
            $data = json_decode($content, true);
            
            if ($data === null) {
                return ['success' => false, 'message' => 'Error leyendo archivo JSON'];
            }
            
            return ['success' => true, 'data' => $data, 'message' => 'Progreso cargado exitosamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Guardar rutina completada en historial
     */
    public function saveCompletedWorkout($data) {
        try {
            $date = date('Y-m-d');
            $filename = $this->userDir . 'history/' . $date . '.json';
            
            // Agregar metadatos
            $data['savedAt'] = date('c');
            $data['userId'] = $this->userId;
            $data['date'] = $date;
            
            $result = file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
            
            if ($result !== false) {
                return ['success' => true, 'message' => 'Rutina guardada en historial', 'file' => $filename];
            } else {
                return ['success' => false, 'message' => 'Error escribiendo archivo de historial'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cargar historial completo
     */
    public function loadHistory() {
        try {
            $historyDir = $this->userDir . 'history/';
            $files = glob($historyDir . '*.json');
            
            $history = [];
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                
                if ($data !== null) {
                    $date = basename($file, '.json');
                    $history[$date] = $data;
                }
            }
            
            // Ordenar por fecha (más reciente primero)
            krsort($history);
            
            return ['success' => true, 'data' => $history, 'message' => 'Historial cargado exitosamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los datos del usuario
     */
    public function getAllData() {
        $progress = $this->loadProgress();
        $history = $this->loadHistory();
        
        return [
            'success' => true,
            'data' => [
                'userId' => $this->userId,
                'progress' => $progress['data'],
                'history' => $history['data'],
                'exportDate' => date('c'),
                'appVersion' => '1.0.0'
            ]
        ];
    }
    
    /**
     * Limpiar backups antiguos
     */
    private function cleanOldBackups() {
        $backupDir = $this->userDir . 'backups/';
        $files = glob($backupDir . '*.json');
        $cutoffTime = time() - (BACKUP_RETENTION_DAYS * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
    
    /**
     * Limpiar progreso actual
     */
    public function clearProgress() {
        try {
            $filename = $this->userDir . 'progress/current_progress.json';
            
            if (file_exists($filename)) {
                // Crear backup antes de eliminar
                $backupName = $this->userDir . 'backups/progress_cleared_' . date('Y-m-d_H-i-s') . '.json';
                copy($filename, $backupName);
                unlink($filename);
            }
            
            return ['success' => true, 'message' => 'Progreso eliminado exitosamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

// Procesar la petición
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validar userId
    if (!isset($input['userId']) || empty($input['userId'])) {
        throw new Exception('ID de usuario requerido');
    }
    
    $handler = new WorkoutDataHandler($input['userId']);
    $response = ['success' => false, 'message' => 'Acción no reconocida'];
    
    switch ($method) {
        case 'POST':
            $action = isset($input['action']) ? $input['action'] : '';
            
            switch ($action) {
                case 'saveProgress':
                    if (isset($input['data'])) {
                        $response = $handler->saveProgress($input['data']);
                    } else {
                        $response = ['success' => false, 'message' => 'Datos de progreso requeridos'];
                    }
                    break;
                    
                case 'loadProgress':
                    $response = $handler->loadProgress();
                    break;
                    
                case 'saveWorkout':
                    if (isset($input['data'])) {
                        $response = $handler->saveCompletedWorkout($input['data']);
                    } else {
                        $response = ['success' => false, 'message' => 'Datos de rutina requeridos'];
                    }
                    break;
                    
                case 'loadHistory':
                    $response = $handler->loadHistory();
                    break;
                    
                case 'getAllData':
                    $response = $handler->getAllData();
                    break;
                    
                case 'clearProgress':
                    $response = $handler->clearProgress();
                    break;
                    
                default:
                    $response = ['success' => false, 'message' => 'Acción no válida: ' . $action];
            }
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Método HTTP no soportado'];
    }
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
}

echo json_encode($response);
?>