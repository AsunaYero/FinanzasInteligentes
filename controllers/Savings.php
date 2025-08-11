<?php
require_once("../config/db.php");

//se define la clase para manejar operaciones relacionadas con ahorros
class SavingsController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    // metodo privado para ejecutar consultas SQL (general)
    private function ejecutarConsulta($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params); //prepara la consulta con parametros
        if ($stmt && sqlsrv_execute($stmt)) { //si se prepara y ejecuta correctamente
            return $stmt; //devuelve el resultado
        }
        return false; //si falla, devuelve false    
    }

    //Metodo privado para ejecutar consultas tipo INSERT
    private function ejecutarConsultaInsert($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            return $stmt;
        }
        return false;
    }

    //metodo para obtener todos los ahorros del usuario
    public function getTotalSavingsByUserId($userId){
        $sql = "SELECT SUM(amount) AS total FROM savings WHERE user_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$userId]);

        if ($stmt){
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC); //trae la fila como array asociativo
            return $row['total'] ?? 0; //devuelve el total o 0 si no hay resultado
        }
        return 0; //si no hay resultado, devuelve 0
    }

    //metodo para 
    public function getSavingsByUserId($userId) {
    $sql = "SELECT 
            s.saving_id,
                    c.name AS category_name,
                    s.amount,
                    s.created_date,
                    s.withdraw_date
                FROM savings s
                INNER JOIN categories c ON s.category_id = c.category_id
                WHERE s.user_id = ?";

        $stmt = $this->ejecutarConsulta($sql, [$userId]);

        $savings = []; //Array para almacenar los ahorros
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $savings[] = $row; // Agrega cada ahorro al array
            }
        }
        return $savings; // Devuelve el array de ahorros
    }

    //Consulta para traer las categorias de tipo savings
    public function getSavingsCategories() {
    $sql = "SELECT category_id, name FROM categories WHERE type = 'saving'";
        $stmt = $this->ejecutarConsulta($sql);

        $categories = []; //Array para almacenar las categorias
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $categories[] = $row; // Agrega cada categoria al array
            }
        }
        return $categories; // Devuelve el array de categorias
    }

    //metodo para obtener la suma de los ahorros del usuario por el tipo de categoria
    public function getTotalSavingsByCategory($userId, $categoryId) {
        $sql = "SELECT SUM(amount) AS total FROM savings WHERE user_id = ? AND category_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$userId, $categoryId]);

        if ($stmt) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC); // Trae la fila como array asociativo
            return $row['total'] ?? 0; // Devuelve el total o 0 si no hay resultado
        }
        return 0; // Si no hay resultado, devuelve 0
    }


    //agregar un nuevo ahorro a la base de datos
    public function agregarSavings($userId, $category_id, $amount, $withdraw_date) {
    $sql = "INSERT INTO savings (user_id, category_id, amount, created_date, withdraw_date) VALUES (?, ?, ?, GETDATE(), ?)";
    return $this->ejecutarConsultaInsert($sql, [$userId, $category_id, $amount, $withdraw_date]) !== false;
    }    
    

    //obtener un ahorro por su ID y el ID del usuario
    public function getSavingsById($savingsId, $userId) {
    error_log("DEBUG: getSavingsById - savings ID: $savingsId, User ID: $userId");

    $sql = "SELECT s.*, c.name AS category_name 
        FROM savings s
        INNER JOIN categories c ON s.category_id = c.category_id
        WHERE s.saving_id = ? AND s.user_id = ?";
    $stmt = $this->ejecutarConsulta($sql, [$savingsId, $userId]);

    if ($stmt) {
        $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        error_log("DEBUG: getSavingsById - Resultado: " . ($result ? 'encontrado' : 'no encontrado'));
        if ($result) {
            error_log("DEBUG: getSavingsById - Datos: " . print_r($result, true));
        }
        return $result;
    }
    error_log("DEBUG: getSavingsById - Error en la consulta");
    return false;
    }  

    //actualizar un ahorro existente
    public function actualizarSavings($savingsId, $userId, $category_id, $amount, $withdraw_date) {
    $sql = "UPDATE savings SET category_id = ?, amount = ?, withdraw_date = ? WHERE saving_id = ? AND user_id = ?";
    return $this->ejecutarConsulta($sql, [$category_id, $amount, $withdraw_date, $savingsId, $userId]) !== false;
    }

    //eliminar un ahorro por su ID y el ID del usuario
    public function eliminarSavings($savingsId, $userId) {
    $sql = "DELETE FROM savings WHERE saving_id = ? AND user_id = ?";
    return $this->ejecutarConsulta($sql, [$savingsId, $userId]) !== false;
    }   
}   

//----------Manejo de peticiones AJAX para ahorros----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once("../config/functions.php");

    if (!isAuthenticated()) {
        jsonResponse(false, 'User not authenticated');
    }

    $userId = getCurrentUserId();
    $savingsController = new SavingsController($conn);

    if (isset($_POST['action'])) {
    error_log('POST: ' . print_r($_POST, true));
        switch ($_POST['action']) {
            case 'agregar':
                $category_id = intval($_POST['category_id'] ?? 0);
                $amount = floatval($_POST['amount'] ?? 0);
                $withdraw_date = $_POST['withdraw_date'] ?? null;
                if ($category_id <= 0) {
                    jsonResponse(false, 'Category is required');
                }
                if ($amount <= 0) {
                    jsonResponse(false, 'Amount must be positive');
                }
                if (!$withdraw_date) {
                    jsonResponse(false, 'Withdraw date is required');
                }
                $success = $savingsController->agregarSavings($userId, $category_id, $amount, $withdraw_date);
                $message = $success ? 'Saving added successfully' : 'Error adding saving';
                jsonResponse($success, $message);
                break;

            case 'actualizar':
                $savingsId = intval($_POST['saving_id'] ?? 0);
                $category_id = intval($_POST['category_id'] ?? 0);
                $amount = floatval($_POST['amount'] ?? 0);
                $withdraw_date = $_POST['withdraw_date'] ?? null;
                if ($savingsId <= 0) {
                    jsonResponse(false, 'Invalid saving ID');
                }
                if ($category_id <= 0) {
                    jsonResponse(false, 'Category is required');
                }
                if ($amount <= 0) {
                    jsonResponse(false, 'Amount must be positive');
                }
                if (!$withdraw_date) {
                    jsonResponse(false, 'Withdraw date is required');
                }
                $success = $savingsController->actualizarSavings($savingsId, $userId, $category_id, $amount, $withdraw_date);
                $message = $success ? 'Saving updated successfully' : 'Error updating saving';
                jsonResponse($success, $message);
                break;

            case 'eliminar':
                $savingsId = intval($_POST['saving_id'] ?? 0);
                if ($savingsId <= 0) {
                    jsonResponse(false, 'Invalid saving ID');
                }
                $success = $savingsController->eliminarSavings($savingsId, $userId);
                $message = $success ? 'Saving deleted successfully' : 'Error deleting saving';
                jsonResponse($success, $message);
                break;

            case 'obtener':
                $savingsId = intval($_POST['saving_id'] ?? 0);
                if ($savingsId <= 0) {
                    jsonResponse(false, 'Invalid saving ID');
                }
                $saving = $savingsController->getSavingsById($savingsId, $userId);
                if ($saving) {
                    jsonResponse(true, 'Saving found', $saving);
                } else {
                    jsonResponse(false, 'Saving not found');
                }
                break;

            default:
                jsonResponse(false, 'Invalid action');
        }
    }
}
?>