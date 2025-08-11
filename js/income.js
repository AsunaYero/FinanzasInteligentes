// Funciones para manejar modales de ingresos
function abrirModalAgregarIncome() {
    document.getElementById('modalAgregarIncome').style.display = 'block';
    document.getElementById('formAgregarIncome').reset();
}

function cerrarModalIncome(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
// Función para mostrar notificaciones
function mostrarNotificacionIncome(mensaje, tipo = 'success') {
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion ${tipo}`;
    notificacion.textContent = mensaje;
    // Estilos de la notificación
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 30000;
        animation: slideIn 0.3s ease-out;
        ${tipo === 'success' ? 'background: linear-gradient(135deg, #28a745, #20c997);' : 'background: linear-gradient(135deg, #dc3545, #fd7e14);'}
    `;
    document.body.appendChild(notificacion);

    // Remover después de 3 segundos
    setTimeout(() => {
        notificacion.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (document.body.contains(notificacion)) {
                document.body.removeChild(notificacion);
            }
        }, 300);
    }, 3000);
}

// Agregar ingreso
function agregarIncome(formData) {
    fetch('../controllers/Income.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacionIncome(data.message || 'Income added successfully', 'success');
            cerrarModalIncome('modalAgregarIncome');
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacionIncome(data.message || 'Error adding income', 'error');
        }
    })
    .catch(() => mostrarNotificacionIncome('Error processing request', 'error'));
}

// Editar ingreso
function editarIncome(incomeId) {
    const formData = new FormData();
    formData.append('action', 'obtener');
    formData.append('income_id', incomeId);

    fetch('../controllers/Income.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_income_id').value = data.data.income_id;
            document.getElementById('edit_category_id').value = data.data.category_id;
            document.getElementById('edit_amount').value = data.data.amount;
            document.getElementById('modalEditarIncome').style.display = 'block';
        } else {
            mostrarNotificacionIncome(data.message || 'Error getting income data', 'error');
        }
    })
    .catch(() => mostrarNotificacionIncome('Error getting income data', 'error'));
}

// Listeners para formularios
document.addEventListener('DOMContentLoaded', () => {
    const formAgregar = document.getElementById('formAgregarIncome');
    if (formAgregar) {
        formAgregar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'agregar');
            agregarIncome(formData);
        });
    }

    const formEditar = document.getElementById('formEditarIncome');
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'actualizar');
            actualizarIncome(formData);
        });
    }
});

// Actualizar ingreso
function actualizarIncome(formData) {
    fetch('../controllers/Income.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacionIncome(data.message || 'Income updated successfully', 'success');
            cerrarModalIncome('modalEditarIncome');
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacionIncome(data.message || 'Error updating income', 'error');
        }
    })
    .catch(() => mostrarNotificacionIncome('Error processing request', 'error'));
}

// Eliminar ingreso
function eliminarIncome(incomeId) {
    if (confirm('Are you sure you want to delete this income?')) {
        const formData = new FormData();
        formData.append('action', 'eliminar');
        formData.append('income_id', incomeId);

        fetch('../controllers/Income.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacionIncome(data.message || 'Income deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacionIncome(data.message || 'Error deleting income', 'error');
            }
        })
        .catch(() => mostrarNotificacionIncome('Error processing request', 'error'));
    }
}


