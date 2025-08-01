<?php require_once 'includes/header.php'; ?>
    <section aria-labelledby="contact-title">
        <div class="breadcrumb">
            <a href="index.php">Inicio</a> > Contacto
        </div>
        
        <h1 id="contact-title">Contacto</h1>
        <p class="intro">Estamos aquí para ayudarle. Complete el formulario o utilice nuestra información de contacto.</p>
        
        <div class="contact-container">
            <div class="contact-info">
                <h2>Información de Contacto</h2>
                <ul class="contact-details">
                    <li>
                        <span class="icon">📍</span>
                        <span>Av. Amazonas N34-451 y Av. Atahualpa, Quito, Ecuador</span>
                    </li>
                    <li>
                        <span class="icon">📞</span>
                        <span>+593 2 123 4567</span>
                    </li>
                    <li>
                        <span class="icon">✉️</span>
                        <span>info@cooperativatransporte.com</span>
                    </li>
                    <li>
                        <span class="icon">🕒</span>
                        <span>Lunes a Domingo: 5:00 am - 10:00 pm</span>
                    </li>
                </ul>
                
                <div class="social-media">
                    <h3>Síganos en redes sociales</h3>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><span>f</span></a>
                        <a href="#" aria-label="Twitter"><span>t</span></a>
                        <a href="#" aria-label="Instagram"><span>i</span></a>
                        <a href="#" aria-label="WhatsApp"><span>w</span></a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <h2>Formulario de Contacto</h2>
                <form id="contact-form" method="post">
                    <div class="form-group">
                        <label for="name">Nombre Completo *</label>
                        <input type="text" id="name" name="name" required aria-required="true">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" required aria-required="true">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Asunto *</label>
                        <select id="subject" name="subject" required aria-required="true">
                            <option value="">Seleccione un asunto</option>
                            <option value="reserva">Consulta sobre reservas</option>
                            <option value="reclamo">Reclamos</option>
                            <option value="sugerencia">Sugerencias</option>
                            <option value="empresarial">Servicios empresariales</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mensaje *</label>
                        <textarea id="message" name="message" rows="5" required aria-required="true"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="map-container">
            <h2>Ubicación de Nuestras Oficinas</h2>
            <div class="map" aria-label="Mapa de ubicación de oficinas">
                <!-- Imagen de mapa sería implementada aquí -->
                <img src="/proyecto_cooperativa/assets/img/mapa-oficinas.jpg" alt="Mapa mostrando ubicación de oficinas en Quito" class="map-image">
            </div>
        </div>
    </section>
<?php require_once 'includes/footer.php'; ?>