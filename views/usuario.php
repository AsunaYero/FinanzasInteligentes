<?php 
    require_once("master/header.php");
    require_once("../config/functions.php");
    require_once("../controllers/Savings.php");
    require_once("../controllers/Income.php");
    require_once("../controllers/Activos.php");
    
// Verificar autenticación
requireAuth();

// Verificamos si el usuario está autenticado y tiene nombre
$nombreUsuario = getCurrentUserName();
$SavingsController = new SavingsController($conn);
$totalSavings = $SavingsController->getTotalSavingsByUserId($_SESSION['user_id']);
$incomeController = new IncomeController($conn);
$totalIncome = $incomeController->getTotalIncomeByUserId($_SESSION['user_id']);
$activosController = new ActivosController($conn); 
$totalActivos = $activosController->getTotalActivosByUserId($_SESSION['user_id']);
?>

<body>
    <link rel="stylesheet" href="../css/user.css">
    <!-- Dashboard principal -->
    <div class="dashboard">
        <div class="card blue">
            <div class="icon"><i class="bi bi-cash"></i></div>
            <div class="content">
                <h4>Ingreso Total</h4>
                <p>$<?php echo number_format($totalIncome, 2); ?></p>
            </div>
        </div>
        <div class="card red">
            <div class="icon"><i class="bi bi-graph-down"></i></div>
            <div class="content">
                <h4>Gasto Total</h4>
                <p>0.00</p>
            </div>
        </div>
        <div class="card green">
            <div class="icon"><i class="bi bi-bar-chart-line"></i></div>
            <div class="content">
                <h4>Ahorro Total</h4>
                <p>$<?php echo number_format($totalSavings, 2); ?></p>
            </div>
        </div>
        <div class="card yellow">
            <div class="icon"><i class="bi bi-pie-chart"></i></div>
            <div class="content">
                <h4>Activo Total</h4>
                <p>$<?php echo number_format($totalActivos, 2); ?></p>
            </div>
        </div>
    </div>
</body>