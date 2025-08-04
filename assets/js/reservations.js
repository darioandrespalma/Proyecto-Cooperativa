/**
 * JavaScript para la página de Mis Reservas
 * Incluye validaciones, interacciones y funcionalidades específicas
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeReservationsPage();
});

/**
 * Inicializa todas las funcionalidades de la página
 */
function initializeReservationsPage() {
    setupFormValidation();
    setupKeyboardShortcuts();
    setupModalEvents();
}

/**
 * Configuración de validación del formulario
 */
function setupFormValidation() {
    const cedulaField = document.getElementById('cedula');
    const searchForm = document.getElementById('reservation-search');
    
    if (cedulaField && searchForm) {
        // Validación en tiempo real
        cedulaField.addEventListener('input', function() {
            validateCedulaField(this);
        });
        
        // Búsqueda al presionar Enter
        searchForm.addEventListener('submit', function(e) {
            if (!validateCedulaField(cedulaField)) {
                e.preventDefault();
            }
        });

        // Solo permitir números
        cedulaField.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
                e.preventDefault();
            }
        });
        
        // Limitar a 10 caracteres
        cedulaField.addEventListener('input', function() {
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    }
}

/**
 * Valida el campo de cédula, asegurando que solo sean 10 dígitos.
 */
function validateCedulaField(field) {
    const formGroup = field.closest('.form-group');
    const cedula = field.value.trim();
    
    clearFieldError(formGroup);
    
    if (cedula.length !== 10) {
        showFieldError(formGroup, 'La cédula debe tener exactamente 10 dígitos.');
        return false;
    }
    
    showFieldSuccess(formGroup);
    return true;
}

/**
 * Muestra error en un campo
 */
function showFieldError(formGroup, message) {
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
    
    let errorElement = formGroup.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.setAttribute('role', 'alert');
        formGroup.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

/**
 * Muestra éxito en un campo
 */
function showFieldSuccess(formGroup) {
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
    clearFieldError(formGroup);
}

/**
 * Limpia errores de un campo
 */
function clearFieldError(formGroup) {
    formGroup.classList.remove('error', 'success');
    const errorElement = formGroup.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

/**
 * Configuración de atajos de teclado
 */
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Escape para cerrar modales
        if (e.key === 'Escape') {
            closeAllModals();
        }
        
        // Ctrl/Cmd + F para enfocar búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const cedulaField = document.getElementById('cedula');
            if (cedulaField) {
                cedulaField.focus();
                cedulaField.select();
            }
        }
    });
}

/**
 * Configuración de eventos de modales
 */
function setupModalEvents() {
    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });
}

/**
 * Alterna la visibilidad de los detalles de una reserva
 */
function toggleDetails(reservationId) {
    const detailsDiv = document.getElementById(`expanded-details-${reservationId}`);
    const button = document.querySelector(`[onclick="toggleDetails(${reservationId})"]`);
    
    if (!detailsDiv || !button) return;
    
    const isVisible = detailsDiv.style.display !== 'none';
    
    if (isVisible) {
        detailsDiv.style.display = 'none';
        button.innerHTML = '<span class="btn-icon">👁️</span> Ver Detalles';
        button.setAttribute('aria-expanded', 'false');
    } else {
        detailsDiv.style.display = 'block';
        button.innerHTML = '<span class="btn-icon">👁️</span> Ocultar Detalles';
        button.setAttribute('aria-expanded', 'true');
        
        detailsDiv.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'nearest' 
        });
    }
}

/**
 * Abre el modal de modificación de reserva
 */
function openModifyModal(reservationId) {
    const modal = document.getElementById('modify-modal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    const confirmButton = document.getElementById('confirm-modify');
    if (confirmButton) {
        confirmButton.onclick = function() {
            window.location.href = `modify_reservation.php?id=${reservationId}`;
        };
    }
}

/**
 * Abre el modal de cancelación de reserva
 */
function openCancelModal(reservationId) {
    const modal = document.getElementById('cancel-modal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    
    const confirmButton = document.getElementById('confirm-cancel');
    if (confirmButton) {
        confirmButton.onclick = function() {
            const reason = document.getElementById('cancel-reason').value;
            console.log(`Cancelando reserva ${reservationId} con motivo: ${reason}`);
            showLoadingState(confirmButton);
            setTimeout(() => {
                hideLoadingState(confirmButton);
                closeModal('cancel-modal');
                alert('La solicitud de cancelación ha sido enviada.');
            }, 1000);
        };
    }
}

/**
 * Cierra un modal específico
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

/**
 * Cierra todos los modales abiertos
 */
function closeAllModals() {
    document.querySelectorAll('.modal[style*="flex"]').forEach(modal => {
        closeModal(modal.id);
    });
}

/**
 * Muestra estado de carga en un botón
 */
function showLoadingState(button) {
    if (!button) return;
    
    button.disabled = true;
    button.classList.add('loading');
    button.originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner"></span> Procesando...';
}

/**
 * Oculta estado de carga en un botón
 */
function hideLoadingState(button) {
    if (!button) return;
    
    button.disabled = false;
    button.classList.remove('loading');
    if (button.originalText) {
        button.innerHTML = button.originalText;
    }
}

// Exportar funciones para uso global
window.toggleDetails = toggleDetails;
window.openModifyModal = openModifyModal;
window.openCancelModal = openCancelModal;
window.closeModal = closeModal;