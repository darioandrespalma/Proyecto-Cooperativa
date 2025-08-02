</main>
    <footer class="site-footer" role="contentinfo">
        <div class="footer-container">
            <!-- Sección de información de contacto -->
            <div class="footer-section contact-info">
                <h2 class="footer-heading">Contacto</h2>
                <address>
                    <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Av. Amazonas N34-451, Quito, Ecuador</p>
                    <p><i class="fas fa-phone" aria-hidden="true"></i> <a href="tel:+59321234567">+593 2 123 4567</a></p>
                    <p><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:info@cooperativatransporte.com">info@cooperativatransporte.com</a></p>
                </address>
            </div>
            
            <!-- Sección de enlaces rápidos -->
            <div class="footer-section quick-links">
                <h2 class="footer-heading">Enlaces Rápidos</h2>
                <nav aria-label="Enlaces secundarios">
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="routes_info.php">Rutas y Horarios</a></li>
                        <li><a href="faq.php">Preguntas Frecuentes</a></li>
                        <li><a href="terms.php">Términos y Condiciones</a></li>
                        <li><a href="privacy.php">Política de Privacidad</a></li>
                    </ul>
                </nav>
            </div>
            
            <!-- Sección de accesibilidad -->
            <div class="footer-section accessibility-tools">
                <h2 class="footer-heading">Accesibilidad</h2>
                <div class="accessibility-controls">
                    <button id="contrast-toggle" class="accessibility-btn" aria-label="Cambiar a modo de alto contraste" aria-pressed="false">
                        <i class="fas fa-adjust" aria-hidden="true"></i> Contraste
                    </button>
                    <button id="font-increase" class="accessibility-btn" aria-label="Aumentar tamaño de texto">
                        <i class="fas fa-font" aria-hidden="true"></i> A+
                    </button>
                    <button id="font-decrease" class="accessibility-btn" aria-label="Disminuir tamaño de texto">
                        <i class="fas fa-font" aria-hidden="true"></i> A-
                    </button>
                    <button id="reset-styles" class="accessibility-btn" aria-label="Restablecer estilos originales">
                        <i class="fas fa-undo" aria-hidden="true"></i> Reset
                    </button>
                </div>
                <p class="accessibility-notice">Este sitio cumple con los estándares WCAG 2.1 AA</p>
            </div>
            
            <!-- Sección de redes sociales -->
            <div class="footer-section social-media">
                <h2 class="footer-heading">Síguenos</h2>
                <div class="social-icons" role="list">
                    <a href="#" class="social-icon" aria-label="Facebook" role="listitem"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon" aria-label="Twitter" role="listitem"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon" aria-label="Instagram" role="listitem"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon" aria-label="WhatsApp" role="listitem"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Copyright y créditos -->
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Cooperativa de Transporte Espejo. Todos los derechos reservados.</p>
            <p class="credits">Sistema de reservas v2.0 - Desarrollado con <i class="fas fa-heart" aria-hidden="true"></i> Master Software</p>
        </div>
    </footer>
    
    <!-- Scripts de accesibilidad -->
    <script src="assets/js/a11y.js"></script>
    
</body>
</html>