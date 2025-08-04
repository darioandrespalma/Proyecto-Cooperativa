/**
 * a11y.js
 * Script para mejorar la accesibilidad del sitio web.
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Alternar alto contraste
    setupContrastToggle();
    
    // 2. Control de tamaño de fuente
    setupFontSizeControls();
    
    // 3. Navegación por teclado mejorada y atajos
    setupKeyboardNavigation();

    // 4. Enfocar elementos dinámicos con MutationObserver
    setupLiveRegionFocus();
    
    // 5. Menú móvil accesible
    setupMobileMenu();
});

/**
 * Configura la funcionalidad de alternar el alto contraste.
 */
function setupContrastToggle() {
    const contrastToggle = document.getElementById('contrast-toggle');
    if (contrastToggle) {
        contrastToggle.addEventListener('click', function() {
            document.body.classList.toggle('high-contrast');
            const isHighContrast = document.body.classList.contains('high-contrast');
            this.setAttribute('aria-pressed', isHighContrast);
            localStorage.setItem('highContrast', isHighContrast);
        });
        
        // Cargar preferencia guardada
        if (localStorage.getItem('highContrast') === 'true') {
            document.body.classList.add('high-contrast');
            contrastToggle.setAttribute('aria-pressed', 'true');
        }
    }
}

/**
 * Configura los botones para controlar el tamaño de la fuente.
 */
function setupFontSizeControls() {
    const fontIncrease = document.getElementById('font-increase');
    const fontDecrease = document.getElementById('font-decrease');
    
    // Cargar tamaño de fuente guardado
    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        document.documentElement.style.fontSize = savedFontSize + 'px';
    }
    
    if (fontIncrease && fontDecrease) {
        fontIncrease.addEventListener('click', () => changeFontSize(1));
        fontDecrease.addEventListener('click', () => changeFontSize(-1));
    }
}

/**
 * Cambia el tamaño de la fuente de la raíz del documento.
 * @param {number} direction - 1 para aumentar, -1 para disminuir.
 */
function changeFontSize(direction) {
    const html = document.documentElement;
    const currentSize = parseFloat(getComputedStyle(html).fontSize);
    let newSize = currentSize + (direction * 2);
    
    // Limitar entre 12px y 24px
    if (newSize < 12) newSize = 12;
    if (newSize > 24) newSize = 24;

    html.style.fontSize = newSize + 'px';
    localStorage.setItem('fontSize', newSize);
}

/**
 * Configura la navegación por teclado mejorada y los atajos.
 */
function setupKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        // Atajo: Alt + S (o Ctrl + S) para saltar al contenido principal
        if (e.key === 's' && (e.ctrlKey || e.altKey)) {
            const main = document.querySelector('main');
            if (main) {
                main.tabIndex = -1;
                main.focus();
                e.preventDefault();
            }
        }
    });
}

/**
 * Utiliza MutationObserver para enfocar regiones dinámicas del DOM
 * cuando su contenido cambia.
 */
function setupLiveRegionFocus() {
    const liveRegions = document.querySelectorAll('[aria-live]');
    
    liveRegions.forEach(region => {
        // Crear una instancia de MutationObserver
        const observer = new MutationObserver(function(mutationsList) {
            // Solo actuar si se han añadido nodos al árbol
            const hasNewNodes = mutationsList.some(mutation => mutation.addedNodes.length > 0);
            if (hasNewNodes) {
                // Enfocar la región para que los lectores de pantalla la anuncien
                region.setAttribute('tabindex', '-1');
                region.focus();
            }
        });

        // Configuración para observar cambios en los hijos del elemento y sus subárboles
        const config = { childList: true, subtree: true };
        
        // Comienza la observación
        observer.observe(region, config);
    });
}

/**
 * Configura el menú de navegación móvil para que sea accesible.
 */
function setupMobileMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainMenu = document.getElementById('main-menu');
    
    if (menuToggle && mainMenu) {
        menuToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            mainMenu.classList.toggle('show');
            
            // Gestionar el foco del menú
            if (!expanded) {
                // Si el menú se abre, enfocar el primer elemento
                const firstMenuItem = mainMenu.querySelector('a, button');
                if (firstMenuItem) {
                    firstMenuItem.focus();
                }
            } else {
                // Si el menú se cierra, devolver el foco al botón de toggle
                this.focus();
            }
        });

        // Navegación por teclado en el menú
        mainMenu.addEventListener('keydown', function(e) {
            const focusableElements = Array.from(mainMenu.querySelectorAll('a, button'));
            const firstItem = focusableElements[0];
            const lastItem = focusableElements[focusableElements.length - 1];

            if (e.key === 'Tab') {
                if (e.shiftKey && document.activeElement === firstItem) {
                    e.preventDefault();
                    menuToggle.focus();
                } else if (!e.shiftKey && document.activeElement === lastItem) {
                    e.preventDefault();
                    // Opcional: Mover el foco al siguiente elemento después del menú
                    // Por simplicidad, aquí lo volvemos a enfocar el toggle
                    menuToggle.focus();
                }
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                menuToggle.click(); // Simular clic para cerrar
            }
        });
    }
}