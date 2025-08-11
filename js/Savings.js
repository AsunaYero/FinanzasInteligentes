// Funciones para manejar modales de ingresos
function abrirModalAgregarSaving() {
    document.getElementById('modalAgregarSaving').style.display = 'block';
    document.getElementById('formAgregarSaving').reset();
}

function cerrarModalSaving(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Función para mostrar notificaciones
function mostrarNotificacionSaving(mensaje, tipo = 'success') {
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

// Agregar ahorro
function agregarSaving(formData) {
    fetch('../controllers/Savings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacionSaving(data.message || 'Saving added successfully', 'success');
            cerrarModalSaving('modalAgregarSaving');
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacionSaving(data.message || 'Error adding Saving', 'error');
        }
    })
    .catch(() => mostrarNotificacionSaving('Error processing request', 'error'));
}

// Editar ingreso
function editarSaving(incomeId) {
    const formData = new FormData();
    formData.append('action', 'obtener');
    formData.append('saving_id', savingId);

    fetch('../controllers/Savings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_saving_id').value = data.data.income_id;
            document.getElementById('edit_category_id').value = data.data.category_id;
            document.getElementById('edit_amount').value = data.data.amount;
            document.getElementById('edit_withdraw_date').value = data.data.withdraw_date;
            document.getElementById('modalEditarSaving').style.display = 'block';
        } else {
            mostrarNotificacionSaving(data.message || 'Error getting income data', 'error');
        }
    })
    .catch(() => mostrarNotificacionSaving('Error getting income data', 'error'));
}


// Obtener ahorro por ID y mostrar en modal de edición
function obtenerSaving(savingId) {
    const formData = new FormData();
    formData.append('action', 'obtener');
    formData.append('saving_id', savingId);
    fetch('../controllers/Savings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_saving_id').value = data.data.saving_id;
            document.getElementById('edit_category_id').value = data.data.category_id;
            document.getElementById('edit_amount').value = data.data.amount;
            document.getElementById('edit_withdraw_date').value = data.data.withdraw_date;
            document.getElementById('modalEditarSaving').style.display = 'block';
        } else {
            mostrarNotificacionSaving(data.message || 'Error getting saving data', 'error');
        }
    })
    .catch(() => mostrarNotificacionSaving('Error getting saving data', 'error'));
}

// Actualizar ahorro
function actualizarSaving(formData) {
    fetch('../controllers/Savings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacionSaving(data.message || 'Saving updated successfully', 'success');
            cerrarModalSaving('modalEditarSaving');
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacionSaving(data.message || 'Error updating Saving', 'error');
        }
    })
    .catch(() => mostrarNotificacionSaving('Error processing request', 'error'));
}

// Eliminar ahorro
function eliminarSaving(savingId) {
    if (confirm('Are you sure you want to delete this saving?')) {
        const formData = new FormData();
        formData.append('action', 'eliminar');
        formData.append('saving_id', savingId);
        fetch('../controllers/Savings.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacionSaving(data.message || 'Saving deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacionSaving(data.message || 'Error deleting saving', 'error');
            }
        })
        .catch(() => mostrarNotificacionSaving('Error processing request', 'error'));
    }
}

// Listeners para formularios
document.addEventListener('DOMContentLoaded', () => {
    const formAgregar = document.getElementById('formAgregarSaving');
    if (formAgregar) {
        formAgregar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'agregar');
            // Asegurarse de enviar withdraw_date
            const withdrawDate = this.querySelector('[name="withdraw_date"]').value;
            formData.set('withdraw_date', withdrawDate);
            agregarSaving(formData);
        });
    }

    const formEditar = document.getElementById('formEditarSaving');
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'actualizar');
            // Asegurarse de enviar withdraw_date correctamente
            const withdrawDate = this.querySelector('[name="withdraw_date"]').value;
            formData.set('withdraw_date', withdrawDate);
            actualizarSaving(formData);
        });
    }
});