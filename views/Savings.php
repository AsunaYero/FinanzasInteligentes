<?php
require_once("master/header.php");
require_once("../config/db.php");
require_once("../config/functions.php");
require_once("../controllers/Savings.php");

requireAuth();

$SavingsController = new SavingsController($conn);
$totalSavings = $SavingsController->getTotalSavingsByUserId($_SESSION['user_id']);
$savings = $SavingsController->getSavingsByUserId($_SESSION['user_id']);
$savingsCategories = $SavingsController->getSavingsCategories();

?>

<body>
    <link rel="stylesheet" href="../css/activos.css">
    <div class="content">
        <!-- Sección de ingreso total -->
        <div class="active-summary">
            <div class="saldo">
                <h2><i class="bi bi-wallet2"></i>Total Savings</h2>
                <p>$<?php echo number_format($totalSavings, 2); ?></p>
            </div>
        </div>

        <!-- Totales por Categoría en Bloques -->
        <h2 class="section-title">Savings Categories</h2>
        <div class="savings-cards-container" style="display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 32px;">
            <?php
            $totalGeneral = $SavingsController->getTotalSavingsByUserId($_SESSION['user_id']);
            $savingsCategories = $SavingsController->getSavingsCategories();
            foreach ($savingsCategories as $category) {
                $totalByCategory = $SavingsController->getTotalSavingsByCategory($_SESSION['user_id'], $category['category_id']);
                $porcentaje = $totalGeneral > 0 ? ($totalByCategory / $totalGeneral) * 100 : 0;
            ?>
            <div class="savings-card" style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 18px 22px; min-width: 220px; max-width: 260px; flex: 1 1 220px; display: flex; flex-direction: column; align-items: flex-start; border-left: 5px solid #4CAF50;">
                <div style="font-size: 1.1em; font-weight: 600; color: #333; margin-bottom: 6px;">
                    <?php echo htmlspecialchars($category['name']); ?>
                </div>
                <div style="font-size: 1.5em; font-weight: bold; color: #20c997; margin-bottom: 4px;">
                    $<?php echo number_format($totalByCategory, 2); ?>
                </div>
                <div style="font-size: 0.98em; color: #888;">
                    <?php echo number_format($porcentaje, 2); ?>% del total
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- Botón para agregar un nuevo ahorro -->
        <div class="add-asset">            
            <button class="btn-add" onclick="abrirModalAgregarSaving()">
                <i class="bi bi-plus-circle"></i> Add Savings
            </button>  
        </div>        
        
        <!-- Lista de ahorros -->
        <h2 class="section-title">Savings List</h2>
        <div class="table-container">
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Saving No.</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Withdraw Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="savings-tbody">
                    <?php if ($savings && count($savings) > 0): ?>
                        <?php foreach ($savings as $sav): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sav['saving_id']); ?></td>
                                <td><?php echo htmlspecialchars($sav['category_name']); ?></td>
                                <td>$<?php echo number_format($sav['amount'], 2); ?></td>
                                <td><?php echo formatSqlServerDate($sav['created_date']); ?></td>
                                <td><?php echo formatSqlServerDate($sav['withdraw_date']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="obtenerSaving(<?php echo $sav['saving_id']; ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-delete" onclick="eliminarSaving(<?php echo $sav['saving_id']; ?>)" title="Delete">
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
                                    <p>No savings registered</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    

    <!-- Modal Agregar ahorro -->
    <div id="modalAgregarSaving" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalSaving('modalAgregarSaving')">&times;</span>
            <h2><i class="bi bi-plus-circle"></i> Add New Saving</h2>
            <form id="formAgregarSaving">
                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="category_id" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="3">Emergency</option>
                        <option value="6">Vacations</option>
                        <option value="9">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label>Withdraw Date:</label>
                    <input type="date" name="withdraw_date" required>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalSaving('modalAgregarSaving')" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar ahorro -->
    <div id="modalEditarSaving" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalSaving('modalEditarSaving')">&times;</span>
            <h2>Edit Saving</h2>
            <form id="formEditarSaving">
                <input type="hidden" id="edit_saving_id" name="saving_id">
                <div class="form-group">
                    <label>Categoría:</label>
                    <select id="edit_category_id" name="category_id" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="3">Emergency</option>
                        <option value="6">Vacations</option>
                        <option value="9">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" id="edit_amount" name="amount" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label>Withdraw Date:</label>
                    <input type="date" id="edit_withdraw_date" name="withdraw_date" required>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalSaving('modalEditarSaving')" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/Savings.js"></script>

   
</body>
