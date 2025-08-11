<?php
// Configuración de la aplicación
define('APP_NAME', 'FinanzasInteligentes');
define('APP_VERSION', '1.0.0');

// Configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_NAME', 'FinanzasInt');
define('DB_USER', 'Proyectos');
define('DB_PASS', 'Proyecto1*');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora

// URLs
define('BASE_URL', '/finanzasInteligentes');
define('LOGIN_URL', BASE_URL . '/index.html');
define('DASHBOARD_URL', BASE_URL . '/views/usuario.php');
define('ACTIVOS_URL', BASE_URL . '/views/Activos.php');

// Mensajes de error
define('MSG_LOGIN_REQUIRED', 'Debe iniciar sesión para acceder a esta página.');
define('MSG_INVALID_CREDENTIALS', 'Credenciales inválidas.');
define('MSG_REGISTRATION_SUCCESS', 'Usuario registrado exitosamente.');
define('MSG_REGISTRATION_ERROR', 'Error al registrar usuario.');

// Validaciones
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_NAME_LENGTH', 50);
define('MAX_EMAIL_LENGTH', 100);
?> 