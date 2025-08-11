// Funciones para manejar modales
function abrirModalAgregar() {
    document.getElementById('modalAgregar').style.display = 'block';
    // Limpiar formulario
    document.getElementById('formAgregarActivo').reset();
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Crear elemento de notificación
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

// Función para agregar activo
function agregarActivo(formData) {
    fetch('../controllers/Activos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message || 'Asset added successfully', 'success');
            cerrarModal('modalAgregar');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            mostrarNotificacion(data.message || 'Error adding asset', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error processing request', 'error');
    });
}

// Función para editar activo
function editarActivo(assetId) {
    console.log('Editando activo con ID:', assetId);
    
    const formData = new FormData();
    formData.append('action', 'obtener');
    formData.append('asset_id', assetId);
    
    console.log('Enviando petición para obtener activo...');
    
    fetch('../controllers/Activos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        
        if (data.success) {
            console.log('Activo encontrado:', data.data);
            
            const assetIdElement = document.getElementById('edit_asset_id');
            const nombreElement = document.getElementById('edit_nombre');
            const saldoElement = document.getElementById('edit_saldo');
            const modalElement = document.getElementById('modalEditar');
            
            console.log('Elementos encontrados:', {
                assetIdElement: assetIdElement,
                nombreElement: nombreElement,
                saldoElement: saldoElement,
                modalElement: modalElement
            });
            
            if (assetIdElement && nombreElement && saldoElement && modalElement) {
                assetIdElement.value = data.data.asset_id;
                nombreElement.value = data.data.name;
                saldoElement.value = data.data.balance;
                modalElement.style.display = 'block';
                console.log('Modal abierto correctamente');
            } else {
                console.error('No se encontraron todos los elementos del formulario');
                mostrarNotificacion('Error: No se encontraron elementos del formulario', 'error');
            }
        } else {
            console.error('Error al obtener activo:', data.message);
            mostrarNotificacion(data.message || 'Error getting asset data', 'error');
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        mostrarNotificacion('Error getting asset data', 'error');
    });
}

// Función para actualizar activo
function actualizarActivo(formData) {
    fetch('../controllers/Activos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message || 'Asset updated successfully', 'success');
            cerrarModal('modalEditar');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            mostrarNotificacion(data.message || 'Error updating asset', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error processing request', 'error');
    });
}

// Función para eliminar activo
function eliminarActivo(assetId) {
    if (confirm('Are you sure you want to delete this asset?')) {
        const formData = new FormData();
        formData.append('action', 'eliminar');
        formData.append('asset_id', assetId);
        
        fetch('../controllers/Activos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion(data.message || 'Asset deleted successfully', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                mostrarNotificacion(data.message || 'Error deleting asset', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error processing request', 'error');
        });
    }
}

// Event listeners cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Formulario para agregar activo
    const formAgregar = document.getElementById('formAgregarActivo');
    if (formAgregar) {
        formAgregar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'agregar');
            agregarActivo(formData);
        });
    }

    // Formulario para editar activo
    const formEditar = document.getElementById('formEditarActivo');
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'actualizar');
            actualizarActivo(formData);
        });
    }
});

// Estilos CSS para las animaciones de notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style); 