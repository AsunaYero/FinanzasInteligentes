<?php
require_once("../config/db.php");

class ActivosController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function ejecutarConsulta($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            return $stmt;
        }
        return false;
    }

    private function ejecutarConsultaInsert($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            return $stmt;
        }
        return false;
    }

    public function getTotalActivosByUserId($userId) {
        $sql = "SELECT SUM(balance) AS total FROM assets WHERE user_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$userId]);

        if ($stmt) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            return $row['total'] ?? 0;
        }
        return 0;
    }

    public function getActivosByUserId($userId) {
        $sql = "SELECT * FROM assets WHERE user_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$userId]);

        $activos = [];
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $activos[] = $row;
            }
        }
        return $activos;
    }

    public function agregarActivo($userId, $name, $balance) {
        $sql = "INSERT INTO assets (user_id, name, balance, created_date) VALUES (?, ?, ?, GETDATE())";
        return $this->ejecutarConsultaInsert($sql, [$userId, $name, $balance]) !== false;
    }

    public function getActivoById($assetId, $userId) {
        error_log("DEBUG: getActivoById - Asset ID: $assetId, User ID: $userId");
        
        $sql = "SELECT * FROM assets WHERE asset_id = ? AND user_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$assetId, $userId]);

        if ($stmt) {
            $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            error_log("DEBUG: getActivoById - Resultado: " . ($result ? 'encontrado' : 'no encontrado'));
            if ($result) {
                error_log("DEBUG: getActivoById - Datos: " . print_r($result, true));
            }
            return $result;
        }
        error_log("DEBUG: getActivoById - Error en la consulta");
        return false;
    }

    public function actualizarActivo($assetId, $userId, $name, $balance) {
        $sql = "UPDATE assets SET name = ?, balance = ? WHERE asset_id = ? AND user_id = ?";
        return $this->ejecutarConsulta($sql, [$name, $balance, $assetId, $userId]) !== false;
    }

    public function eliminarActivo($assetId, $userId) {
        $sql = "DELETE FROM assets WHERE asset_id = ? AND user_id = ?";
        return $this->ejecutarConsulta($sql, [$assetId, $userId]) !== false;
    }
}

// Manejo de peticiones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once("../config/functions.php");
    
    if (!isAuthenticated()) {
        jsonResponse(false, 'User not authenticated');
    }

    $userId = getCurrentUserId();
    $activosController = new ActivosController($conn);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'agregar':
                $name = trim($_POST['name'] ?? '');
                $balance = floatval($_POST['balance'] ?? 0);
                
                // Validaciones
                if (empty($name)) {
                    jsonResponse(false, 'Asset name is required');
                }
                if ($balance < 0) {
                    jsonResponse(false, 'Balance must be positive');
                }
                
                $success = $activosController->agregarActivo($userId, $name, $balance);
                $message = $success ? 'Asset added successfully' : 'Error adding asset';
                jsonResponse($success, $message);
                break;

            case 'actualizar':
                $assetId = intval($_POST['asset_id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $balance = floatval($_POST['balance'] ?? 0);
                
                // Validaciones
                if ($assetId <= 0) {
                    jsonResponse(false, 'Invalid asset ID');
                }
                if (empty($name)) {
                    jsonResponse(false, 'Asset name is required');
                }
                if ($balance < 0) {
                    jsonResponse(false, 'Balance must be positive');
                }
                
                $success = $activosController->actualizarActivo($assetId, $userId, $name, $balance);
                $message = $success ? 'Asset updated successfully' : 'Error updating asset';
                jsonResponse($success, $message);
                break;

            case 'eliminar':
                $assetId = intval($_POST['asset_id'] ?? 0);
                
                // Validaciones
                if ($assetId <= 0) {
                    jsonResponse(false, 'Invalid asset ID');
                }
                
                $success = $activosController->eliminarActivo($assetId, $userId);
                $message = $success ? 'Asset deleted successfully' : 'Error deleting asset';
                jsonResponse($success, $message);
                break;

            case 'obtener':
                $assetId = intval($_POST['asset_id'] ?? 0);
                
                // Log de depuración
                error_log("DEBUG: Obteniendo activo con ID: $assetId para usuario: $userId");
                
                // Validaciones
                if ($assetId <= 0) {
                    error_log("DEBUG: Asset ID inválido: $assetId");
                    jsonResponse(false, 'Invalid asset ID');
                }
                
                $activo = $activosController->getActivoById($assetId, $userId);
                error_log("DEBUG: Resultado de getActivoById: " . ($activo ? 'encontrado' : 'no encontrado'));
                
                if ($activo) {
                    error_log("DEBUG: Datos del activo: " . print_r($activo, true));
                    
                    // Convertir la fecha para JSON
                    if (isset($activo['created_date']) && $activo['created_date'] instanceof DateTime) {
                        $activo['created_date'] = $activo['created_date']->format('Y-m-d H:i:s');
                        error_log("DEBUG: Fecha convertida para JSON");
                    }
                    
                    error_log("DEBUG: Enviando respuesta exitosa");
                    jsonResponse(true, 'Asset found', $activo);
                } else {
                    error_log("DEBUG: Activo no encontrado");
                    jsonResponse(false, 'Asset not found');
                }
                break;

            default:
                jsonResponse(false, 'Invalid action');
        }
    }
}
