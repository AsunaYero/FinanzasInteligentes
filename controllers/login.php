<?php
require_once("../config/db.php");
require_once("../config/functions.php");
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validaciones básicas
    if (empty($email) || empty($password)) {
        showError('Por favor, complete todos los campos.');
    }

    // Consulta a SQL Server
    $sql = "SELECT * FROM users WHERE email = ?";
    $params = [$email];
    $stmt = prepare($conn, $sql, $params);

    if (sqlsrv_execute($stmt)) {
        // Obtener resultado
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row) {
            // Verificar contraseña
            if (password_verify($password, $row['password'])) {
                //Guardar datos en sesión
                $_SESSION['user_id'] = $row['id'];              
                $_SESSION['Name'] = $row['Name'];   

                // Redirigir al usuario
                header("Location: ../views/usuario.php");
                exit();
            } else {
                showError('Contraseña incorrecta.');
            }
        } else {
            showError('Usuario no encontrado.');
        }
    } else {
        showError('Error en la consulta.');
    }
}
?>
