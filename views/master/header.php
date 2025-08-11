<?php
session_start(); // Muy importante para acceder a $_SESSION

// Verificamos si el usuario está autenticado y tiene nombre
$nombreUsuario = isset($_SESSION['Name']) ? $_SESSION['Name'] : 'Invitado';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/header.css">
    <title>FinanzasInteligentes</title>
</head>
<body>

<header class="header">
    <div class="wrapper">
        <div class="header-left">
            <i class="bi bi-person-circle"></i>
            <div class="user-info">
                <p>Welcome</p> 
                <div class="userName">
                    <strong id="userName"><?php echo htmlspecialchars($nombreUsuario); ?></strong>
                </div>
            </div>
        </div>
        <div class="header-right">
            <a href="../controllers/logout.php" class="logo">SALIR</a>
        </div>
    </div>
</header>

<nav class="navbar">
    <ul class="nav-links">
        <li><a href="../views/usuario.php">Home</a></li>
        <li><a href="#">Gastos</a></li>
        <li><a href="../views/Activos.php">Activos</a></li>
        <li><a href="../views/Savings.php">Savings</a></li>
        <li><a href="../views/Income.php">Income</a></li>
        <li><a href="#">Metas</a></li>
    </ul>
</nav>
