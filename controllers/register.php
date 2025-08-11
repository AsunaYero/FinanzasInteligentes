<?php
require_once('../config/db.php');
require_once('../config/functions.php');

if (isset($_POST['register'])) {
    if (
        !empty($_POST['NameR']) &&
        !empty($_POST['LastNameR']) &&
        !empty($_POST['emailR']) &&
        !empty($_POST['passwordR'])
    ) {
        $Name = sanitizeInput($_POST['NameR']);
        $LastName = sanitizeInput($_POST['LastNameR']);
        $email = sanitizeInput($_POST['emailR']);
        $password = trim($_POST['passwordR']);

        // Validar email
        if (!validateEmail($email)) {
            showError('Correo no válido.');
        }

        // Encriptar la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Consulta SQL
        $sql = "INSERT INTO users (Name, LastName, email, password) VALUES (?, ?, ?, ?); SELECT SCOPE_IDENTITY() as id";
        $params = [$Name, $LastName, $email, $hashedPassword];

        // Preparar la consulta
        $stmt = prepare($conn, $sql, $params);

        // Ejecutar la consulta
        if (sqlsrv_execute($stmt)) {
            // Obtener el ID del usuario recién registrado
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $userId = $row['id'];
            
            // Iniciar sesión automáticamente
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['Name'] = $Name;
            
            header("Location: ../views/usuario.php");
            exit();
        } else {
            showError('Error al registrar.');
        }

    } else {
        showError('Por favor, completa todos los campos.');
    }
}
?>
