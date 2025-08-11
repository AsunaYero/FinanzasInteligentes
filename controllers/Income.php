<?php
require_once("../config/db.php");

// Se define la clase para manejar operaciones relacionadas con ingresos
class IncomeController{
    // Atributo privado para la conexión a la base de datos
    private $conn;

    // Constructor de la clase que recibe la conexión y la asigna al atributo
    public function __construct($conn){
        $this->conn = $conn;
    }

    // Método privado que prepara y ejecuta una consulta SQL (general)
    private function ejecutarConsulta($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params); // Prepara la consulta con parámetros
        if ($stmt && sqlsrv_execute($stmt)) { // Si se prepara y ejecuta correctamente
            return $stmt; // Devuelve el resultado
        }
        return false; // Si falla, devuelve false
    }

    // Método privado para ejecutar una consulta tipo INSERT
    private function ejecutarConsultaInsert($sql, $params = []) {
        $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        if ($stmt && sqlsrv_execute($stmt)) {
            return $stmt;
        }
        return false;
    }

    // Obtiene la suma total de ingresos de un usuario
    public function getTotalIncomeByUserId($userId) {
        $sql = "SELECT SUM(amount) AS total FROM income WHERE user_id = ?";
        $stmt = $this->ejecutarConsulta($sql, [$userId]);

        if ($stmt) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC); // Trae la fila como array asociativo
            return $row['total'] ?? 0; // Devuelve el total o 0 si no hay resultado
        }
        return 0;
    }

    // Obtiene todos los activos del usuario por el tipo de categoria
    public function getIncomeByUserId($userId) {
    $sql = "SELECT 
                i.income_id,
                c.name AS category_name,
                i.amount,
                i.received_date
            FROM income i
            INNER JOIN categories c ON i.category_id = c.category_id
            WHERE i.user_id = ?";
    
    $stmt = $this->ejecutarConsulta($sql, [$userId]);

    $income = []; //Array para almacenar los ingresos
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $income[] = $row; // Agrega cada activo al array
        }
    }
    return $income;
    }

    //Consulta para traer las categorias de tipo income
    public function getIncomeCategories() {
    $sql = "SELECT category_id, name FROM categories WHERE type = 'income'";
    $stmt = $this->ejecutarConsulta($sql);

    $categories = [];
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $categories[] = $row;
        }
    }
    return $categories;

    }

     // Agrega un nuevo ingreso a la base de datos
    public function agregarIncome($userId, $category_id, $amount) {
    $sql = "INSERT INTO income (user_id, category_id, amount, received_date) VALUES (?, ?, ?, GETDATE())";
    return $this->ejecutarConsultaInsert($sql, [$userId, $category_id, $amount]) !== false;
    }

    // Obtiene un ingreso específico por su ID y el ID del usuario
    public function getIncomeById($incomeId, $userId) {
    error_log("DEBUG: getIncomeById - income ID: $incomeId, User ID: $userId");

    $sql = "SELECT i.*, c.name AS category_name 
            FROM income i
            INNER JOIN categories c ON i.category_id = c.category_id
            WHERE i.income_id = ? AND i.user_id = ?";
    $stmt = $this->ejecutarConsulta($sql, [$incomeId, $userId]);

    if ($stmt) {
        $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        error_log("DEBUG: getIncomeById - Resultado: " . ($result ? 'encontrado' : 'no encontrado'));
        if ($result) {
            error_log("DEBUG: getIncomeById - Datos: " . print_r($result, true));
        }
        return $result;
    }
    error_log("DEBUG: getIncomeById - Error en la consulta");
    return false;
    }

    // Actualiza un ingreso existente
    public function actualizarIncome($incomeId, $userId, $category_id, $amount) {
        $sql = "UPDATE income 
                SET category_id = ?, amount = ? 
                WHERE income_id = ? AND user_id = ?";
        return $this->ejecutarConsulta($sql, [$category_id, $amount, $incomeId, $userId]) !== false;
    }

    // Elimina un ingreso
    public function eliminarIncome($incomeId, $userId) {
        $sql = "DELETE FROM income WHERE income_id = ? AND user_id = ?";
        return $this->ejecutarConsulta($sql, [$incomeId, $userId]) !== false;
    }

}

// ----------------- MANEJO DE PETICIONES AJAX  ingresos-----------------
// Si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); // Inicia la sesión
    require_once("../config/functions.php"); // Funciones auxiliares

    // Verifica si el usuario está autenticado
    if (!isAuthenticated()) {
        jsonResponse(false, 'User not authenticated');
    }

    // Obtiene el ID del usuario autenticado
    $userId = getCurrentUserId();
    $incomeController = new incomeController($conn); // Crea una instancia del controlador

    // Verifica si hay una acción definida en el POST
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            // Acción: agregar nuevo ingreso
            case 'agregar':
                $category_id = intval($_POST['category_id'] ?? 0);
                $amount = floatval($_POST['amount'] ?? 0);
                if ($category_id <= 0) {
                    jsonResponse(false, 'Category is required');
                }
                if ($amount <= 0) {
                    jsonResponse(false, 'Amount must be positive');
                }
                $success = $incomeController->agregarIncome($userId, $category_id, $amount);
                $message = $success ? 'Income added successfully' : 'Error adding income';
                jsonResponse($success, $message);
                break;

            // Acción: actualizar ingreso existente
            case 'actualizar':
                $incomeId = intval($_POST['income_id'] ?? 0);
                $category_id = intval($_POST['category_id'] ?? 0);
                $amount = floatval($_POST['amount'] ?? 0);
                if ($incomeId <= 0) {
                    jsonResponse(false, 'Invalid income ID');
                }
                if ($category_id <= 0) {
                    jsonResponse(false, 'Category is required');
                }
                if ($amount <= 0) {
                    jsonResponse(false, 'Amount must be positive');
                }
                $success = $incomeController->actualizarIncome($incomeId, $userId, $category_id, $amount);
                $message = $success ? 'Income updated successfully' : 'Error updating income';
                jsonResponse($success, $message);
                break;

            // Acción: eliminar ingreso
            case 'eliminar':
                $incomeId = intval($_POST['income_id'] ?? 0);
                if ($incomeId <= 0) {
                    jsonResponse(false, 'Invalid income ID');
                }
                $success = $incomeController->eliminarIncome($incomeId, $userId);
                $message = $success ? 'Income deleted successfully' : 'Error deleting income';
                jsonResponse($success, $message);
                break;

            // Acción: obtener ingreso por ID
            case 'obtener':
                $incomeId = intval($_POST['income_id'] ?? 0);
                error_log("DEBUG: Obteniendo ingreso con ID: $incomeId para usuario: $userId");
                if ($incomeId <= 0) {
                    error_log("DEBUG: Income ID inválido: $incomeId");
                    jsonResponse(false, 'Invalid income ID');
                }
                $income = $incomeController->getIncomeById($incomeId, $userId);
                error_log("DEBUG: Resultado de getIncomeById: " . ($income ? 'encontrado' : 'no encontrado'));
                if ($income) {
                    error_log("DEBUG: Datos del ingreso: " . print_r($income, true));
                    jsonResponse(true, 'Income found', $income);
                } else {
                    jsonResponse(false, 'Income not found');
                }
                break;

            // Acción no válida
            default:
                jsonResponse(false, 'Invalid action');
        }
    } // ← Cierra el if (isset($_POST['action']))
}