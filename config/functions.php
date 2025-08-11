<?php
require_once 'config.php';

/**
 * Función para verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Función para redirigir al login si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header("Location: " . LOGIN_URL);
        exit();
    }
}

/**
 * Función para obtener el ID del usuario actual
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Función para obtener el nombre del usuario actual
 */
function getCurrentUserName() {
    return $_SESSION['Name'] ?? 'Invitado';
}

/**
 * Función para limpiar y validar entrada de texto
 */
function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

/**
 * Función para validar email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Función para validar longitud de contraseña
 */
function validatePassword($password) {
    return strlen($password) >= MIN_PASSWORD_LENGTH;
}

/**
 * Función para mostrar mensaje de error
 */
function showError($message) {
    echo "<script>alert('$message'); window.history.back();</script>";
}

/**
 * Función para mostrar mensaje de éxito
 */
function showSuccess($message) {
    echo "<script>alert('$message');</script>";
}

/**
 * Función para generar respuesta JSON
 */
function jsonResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

/**
 * Función para registrar actividad del usuario
 */
function logUserActivity($userId, $action, $details = '') {
    // Aquí se podría implementar un sistema de logging
    // Por ahora solo es una función placeholder
    error_log("User $userId performed action: $action - $details");
}

/**
 * Función para formatear fechas de SQL Server
 */
function formatSqlServerDate($date, $format = 'd/m/Y') {
    if ($date instanceof DateTime) {
        return $date->format($format);
    } elseif (is_string($date)) {
        return date($format, strtotime($date));
    } else {
        return 'N/A';
    }
}
?> 