<?php
require_once("master/header.php");
require_once("../config/db.php");
require_once("../config/functions.php");
require_once("../controllers/Income.php");

// Verificar autenticación (asumo que esta función inicia session y verifica)
requireAuth();


$incomeController = new IncomeController($conn);
$totalIncome = $incomeController->getTotalIncomeByUserId($_SESSION['user_id']);
$income = $incomeController->getIncomeByUserId($_SESSION['user_id']);
$incomeCategories = $incomeController->getIncomeCategories();
?>

<body>
    <link rel="stylesheet" href="../css/activos.css">
    <div class="content">
        <!-- Sección de ingreso total -->
        <div class="active-summary">
            <div class="saldo">
                <h2>Total Monthly</h2>
                <p>$ <?php echo number_format($totalIncome, 2); ?></p>
            </div>
            <div class="saldo">
                <h2>Total Annual</h2>
                <p>$ <?php echo number_format($totalIncome, 2); ?></p>
            </div>
        </div>

        <!-- Botón para agregar un nuevo ingreso -->
        <div class="add-asset">
            <button class="btn-add" onclick="abrirModalAgregarIncome()">
                <i class="bi bi-plus-circle"></i> Add Income
            </button>
        </div>

        <!-- Lista de ingresos -->
        <h2 class="section-title">Income List</h2>
        <div class="table-container">
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Income No.</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="activos-tbody">
                    <?php if ($income && count($income) > 0): ?>
                        <?php foreach ($income as $inc): // usar $inc para cada fila ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inc['income_id']); ?></td>
                                <td><?php echo htmlspecialchars($inc['category_name']); ?></td>
                                <td>$<?php echo number_format($inc['amount'], 2); ?></td>
                                <td><?php echo formatSqlServerDate($inc['received_date']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editarIncome(<?php echo $inc['income_id']; ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-delete" onclick="eliminarIncome(<?php echo $inc['income_id']; ?>)" title="Delete">
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
                                    <p>No income registered</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Agregar Ingreso -->
    <div id="modalAgregarIncome" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalIncome('modalAgregarIncome')">&times;</span>
            <h2><i class="bi bi-plus-circle"></i> Add New Income</h2>
            <form id="formAgregarIncome">
                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="category_id" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="1">Salary</option>
                        <option value="4">Freelance</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Monto:</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
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

    <!-- Modal Editar Ingreso -->
    <div id="modalEditarIncome" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalIncome('modalEditarIncome')">&times;</span>
            <h2>Editar Ingreso</h2>
            <form id="formEditarIncome">
                <input type="hidden" id="edit_income_id" name="income_id">

                <label>Categoría:</label>
                <select id="edit_category_id" name="category_id" required>
                    <option value="">Seleccione una categoría</option>
                    <option value="1">Salary</option>
                    <option value="4">Freelance</option>
                </select>

                <label>Monto:</label>
                <input type="number" id="edit_amount" name="amount" step="0.01" min="0.01" required>

                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
    <script src="../js/income.js"></script>

   
</body>
