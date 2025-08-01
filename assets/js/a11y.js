document.addEventListener('DOMContentLoaded', function() {
    // Mejoras de accesibilidad
    
    // 1. Alternar alto contraste
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
    
    // 2. Control de tamaño de fuente
    const fontIncrease = document.getElementById('font-increase');
    const fontDecrease = document.getElementById('font-decrease');
    
    if (fontIncrease && fontDecrease) {
        fontIncrease.addEventListener('click', function() {
            changeFontSize(1);
        });
        
        fontDecrease.addEventListener('click', function() {
            changeFontSize(-1);
        });
    }
    
    function changeFontSize(direction) {
        const html = document.documentElement;
        const currentSize = parseFloat(getComputedStyle(html).fontSize);
        const newSize = currentSize + (direction * 2);
        
        // Limitar entre 12px y 24px
        if (newSize >= 12 && newSize <= 24) {
            html.style.fontSize = newSize + 'px';
            localStorage.setItem('fontSize', newSize);
        }
    }
    
    // Cargar tamaño de fuente guardado
    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        document.documentElement.style.fontSize = savedFontSize + 'px';
    }
    
    // 3. Navegación por teclado mejorada
    document.addEventListener('keydown', function(e) {
        // Saltar al contenido principal
        if (e.key === 's' && e.ctrlKey) {
            const main = document.querySelector('main');
            if (main) {
                main.tabIndex = -1;
                main.focus();
                e.preventDefault();
            }
        }
    });
    
    // 4. Enfocar elementos dinámicos
    const liveRegions = document.querySelectorAll('[aria-live]');
    liveRegions.forEach(region => {
        region.addEventListener('DOMSubtreeModified', function() {
            this.setAttribute('tabindex', '-1');
            this.focus();
        });
    });
    // Menú móvil
    const menuToggle = document.getElementById('menu-toggle');
    const mainMenu = document.getElementById('main-menu');
    
    if (menuToggle && mainMenu) {
        menuToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            mainMenu.classList.toggle('show');
        });
    }
    
    // Navegación por teclado en menú
    if (mainMenu) {
        const menuItems = mainMenu.querySelectorAll('a');
        const firstItem = menuItems[0];
        const lastItem = menuItems[menuItems.length - 1];
        
        menuItems.forEach(item => {
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && e.shiftKey && this === firstItem) {
                    e.preventDefault();
                    menuToggle.focus();
                } else if (e.key === 'Tab' && !e.shiftKey && this === lastItem) {
                    e.preventDefault();
                    // Mover foco al siguiente elemento después del menú
                }
            });
        });
    }
});