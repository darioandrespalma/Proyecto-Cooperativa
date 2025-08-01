// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // ==============================================
    // Funcionalidad del Menú Hamburguesa
    // ==============================================
    const initMobileMenu = () => {
        const menuToggle = document.getElementById('menu-toggle');
        const mainMenu = document.getElementById('main-menu');
        
        if (menuToggle && mainMenu) {
            // Función para alternar el menú
            const toggleMenu = () => {
                const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', !isExpanded);
                mainMenu.setAttribute('aria-expanded', !isExpanded);
                menuToggle.classList.toggle('active');
            };
            
            // Evento click en el botón del menú
            menuToggle.addEventListener('click', toggleMenu);
            
            // Cerrar menú al hacer clic en un enlace (solo móvil)
            const navLinks = document.querySelectorAll('#main-menu a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        menuToggle.setAttribute('aria-expanded', 'false');
                        mainMenu.setAttribute('aria-expanded', 'false');
                        menuToggle.classList.remove('active');
                    }
                });
            });
            
            // Cerrar menú al hacer clic fuera de él
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && 
                    !menuToggle.contains(e.target) && 
                    !mainMenu.contains(e.target) &&
                    mainMenu.getAttribute('aria-expanded') === 'true') {
                    toggleMenu();
                }
            });
        }
    };
    
    // ==============================================
    // Validación de Formularios
    // ==============================================
    const initFormValidation = () => {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let valid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('error');
                        
                        // Crear mensaje de error si no existe
                        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                            const errorMsg = document.createElement('div');
                            errorMsg.classList.add('error-message');
                            errorMsg.textContent = field.dataset.errorMessage || 'Este campo es obligatorio';
                            errorMsg.setAttribute('role', 'alert');
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                    } else {
                        field.classList.remove('error');
                        const errorMsg = field.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains('error-message')) {
                            errorMsg.remove();
                        }
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    // Enfocar el primer campo con error
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.focus();
                        // Desplazarse suavemente al campo con error
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
            
            // Limpiar errores al cambiar los campos
            form.querySelectorAll('[required]').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('error');
                        const errorMsg = this.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains('error-message')) {
                            errorMsg.remove();
                        }
                    }
                });
            });
        });
    };
    
    // ==============================================
    // Inicialización de todas las funcionalidades
    // ==============================================
    initMobileMenu();
    initFormValidation();
    
    // Opcional: Puedes agregar más inicializaciones aquí
    // initOtherFunctionality();
});