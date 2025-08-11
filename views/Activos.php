<?php
require_once("master/header.php");
require_once("../config/db.php");
require_once("../config/functions.php");
require_once("../controllers/Activos.php");

// Verificar autenticación
requireAuth();

$activosController = new ActivosController($conn); 
$totalActivos = $activosController->getTotalActivosByUserId($_SESSION['user_id']);
$activos = $activosController->getActivosByUserId($_SESSION['user_id']);
?>

<body>
    <link rel="stylesheet" href="../css/activos.css">
    <div class="content">
        <!-- Sección de Activo Total -->
        <div class="active-summary">
            <div class="saldo">
                <h2><i class="bi bi-wallet2"></i> Total Assets</h2>
                <p>$<?php echo number_format($totalActivos, 2); ?></p>
            </div>
        </div>


    
        <!-- Botón para agregar activo -->
        <div class="add-asset">
            <button class="btn-add" onclick="abrirModalAgregar()">
                <i class="bi bi-plus-circle"></i> Add Asset
            </button>
        </div>
    
        <!-- Lista de activos -->
        <h2 class="section-title">Assets List</h2>
        <div class="table-container">
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Asset No.</th>
                        <th>Asset Name</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="activos-tbody">
                    <?php if ($activos && count($activos) > 0): ?>
                        <?php foreach ($activos as $activo): ?>
                            <tr>
                                <td><?php echo $activo['asset_id']; ?></td>
                                <td><?php echo htmlspecialchars($activo['name']); ?></td>
                                <td>$<?php echo number_format($activo['balance'], 2); ?></td>
                                <td><?php echo formatSqlServerDate($activo['created_date']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editarActivo(<?php echo $activo['asset_id']; ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-delete" onclick="eliminarActivo(<?php echo $activo['asset_id']; ?>)" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No assets registered</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Agregar Activo -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalAgregar')">&times;</span>
            <h2><i class="bi bi-plus-circle"></i> Add New Asset</h2>
            <form id="formAgregarActivo">
                <div class="form-group">
                    <label for="nombre">Asset Name:</label>
                    <input type="text" id="nombre" name="name" placeholder="Ex: Savings Account" required>
                </div>
                <div class="form-group">
                    <label for="saldo">Balance ($):</label>
                    <input type="number" id="saldo" name="balance" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="cerrarModal('modalAgregar')" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Activo -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalEditar')">&times;</span>
            <h2><i class="bi bi-pencil"></i> Edit Asset</h2>
            <form id="formEditarActivo">
                <input type="hidden" id="edit_asset_id" name="asset_id">
                <div class="form-group">
                    <label for="edit_nombre">Asset Name:</label>
                    <input type="text" id="edit_nombre" name="name" placeholder="Ex: Savings Account" required>
                </div>
                <div class="form-group">
                    <label for="edit_saldo">Balance ($):</label>
                    <input type="number" id="edit_saldo" name="balance" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="cerrarModal('modalEditar')" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/activos.js"></script>
</body>