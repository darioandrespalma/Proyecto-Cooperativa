// Validación específica de campos
function validateCedula(cedula) {
    // Validar cédula ecuatoriana (ejemplo simplificado)
    return /^\d{10}$/.test(cedula);
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
    return /^\d{7,10}$/.test(phone);
}

// Asignar validadores a campos específicos
document.addEventListener('DOMContentLoaded', function() {
    const cedulaField = document.getElementById('cedula');
    if (cedulaField) {
        cedulaField.addEventListener('blur', function() {
            if (!validateCedula(this.value)) {
                this.classList.add('error');
                showError(this, 'Cédula inválida. Debe tener 10 dígitos');
            }
        });
    }
    
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            if (!validateEmail(this.value)) {
                this.classList.add('error');
                showError(this, 'Email inválido');
            }
        });
    }
    
    const phoneField = document.getElementById('celular');
    if (phoneField) {
        phoneField.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                this.classList.add('error');
                showError(this, 'Celular inválido');
            }
        });
    }
    
    function showError(field, message) {
        // Eliminar mensaje anterior si existe
        if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
            field.nextElementSibling.remove();
        }
        
        const errorMsg = document.createElement('div');
        errorMsg.classList.add('error-message');
        errorMsg.textContent = message;
        errorMsg.setAttribute('role', 'alert');
        field.parentNode.insertBefore(errorMsg, field.nextSibling);
    }
});