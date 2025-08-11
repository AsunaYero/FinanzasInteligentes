<?php
require_once 'config.php';

$serverName = DB_SERVER; // o dirección IP o nombre del servidor
$connectionOptions = array(
    "Database" => DB_NAME,
    "Uid" => DB_USER,
    "PWD" => DB_PASS
);

// Establecer conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn) {
    //echo "Conexión exitosa a SQL Server";
} else {
    echo "Error en la conexión:";
    die(print_r(sqlsrv_errors(), true));
}

function prepare($conn, $sql, $params = [])
{
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        echo "Error al preparar la consulta:";
        die(print_r(sqlsrv_errors(), true));
    }

    return $stmt;
}
?>