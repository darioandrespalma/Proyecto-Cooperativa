<?php require_once 'includes/header.php'; ?>
    <section aria-labelledby="help-title">
        <div class="breadcrumb">
            <a href="index.php">Inicio</a> > Ayuda
        </div>
        
        <h1 id="help-title">Centro de Ayuda</h1>
        <p class="intro">Encuentre respuestas a las preguntas más frecuentes y guías para usar nuestro sistema.</p>
        
        <div class="help-container">
            <div class="faq-section">
                <h2>Preguntas Frecuentes</h2>
                
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq1">
                        ¿Cómo puedo reservar un asiento?
                    </button>
                    <div class="faq-answer" id="faq1" hidden>
                        <p>Para reservar un asiento, siga estos pasos:</p>
                        <ol>
                            <li>Regístrese en nuestro sistema o inicie sesión si ya tiene cuenta</li>
                            <li>Seleccione la opción "Reservar Asiento" en el menú principal</li>
                            <li>Elija su ruta, fecha y horario de viaje</li>
                            <li>Seleccione el bus y los asientos que desea reservar</li>
                            <li>Complete el proceso de pago</li>
                            <li>Recibirá un correo de confirmación con los detalles de su reserva</li>
                        </ol>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq2">
                        ¿Cuántos asientos puedo reservar?
                    </button>
                    <div class="faq-answer" id="faq2" hidden>
                        <p>Puede reservar hasta 10 asientos por transacción. Para grupos mayores, contáctenos a través de nuestro formulario de contacto.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq3">
                        ¿Puedo cancelar o modificar mi reserva?
                    </button>
                    <div class="faq-answer" id="faq3" hidden>
                        <p>Sí, puede cancelar o modificar su reserva hasta 24 horas antes de la salida del bus:</p>
                        <ul>
                            <li>Para modificaciones: acceda a "Mis Reservas" y seleccione la opción "Modificar"</li>
                            <li>Para cancelaciones: acceda a "Mis Reservas" y seleccione "Cancelar"</li>
                        </ul>
                        <p>Las cancelaciones están sujetas a nuestra política de reembolsos.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq4">
                        ¿Qué métodos de pago están disponibles?
                    </button>
                    <div class="faq-answer" id="faq4" hidden>
                        <p>Aceptamos los siguientes métodos de pago:</p>
                        <ul>
                            <li>Tarjetas de crédito/débito (Visa, Mastercard, Diners)</li>
                            <li>Transferencias bancarias</li>
                            <li>Pago en efectivo en nuestras oficinas</li>
                        </ul>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq5">
                        ¿Qué debo presentar el día del viaje?
                    </button>
                    <div class="faq-answer" id="faq5" hidden>
                        <p>El día del viaje debe presentar:</p>
                        <ul>
                            <li>Su documento de identificación (cédula o pasaporte)</li>
                            <li>El comprobante de reserva (impreso o digital)</li>
                            <li>Si aplica, el documento que acredite su derecho a descuento</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="guides-section">
                <h2>Guías de Usuario</h2>
                
                <div class="guide-card">
                    <h3>Primeros Pasos</h3>
                    <p>Aprenda cómo registrarse y hacer su primera reserva</p>
                    <a href="#" class="guide-link">Ver guía</a>
                </div>
                
                <div class="guide-card">
                    <h3>Proceso de Pago</h3>
                    <p>Instrucciones detalladas para completar su pago</p>
                    <a href="#" class="guide-link">Ver guía</a>
                </div>
                
                <div class="guide-card">
                    <h3>Gestión de Reservas</h3>
                    <p>Cómo modificar, cancelar o ver sus reservas</p>
                    <a href="#" class="guide-link">Ver guía</a>
                </div>
            </div>
        </div>
        
        <div class="support-section">
            <h2>Soporte Adicional</h2>
            <p>Si no encontró respuesta a su pregunta, contáctenos directamente:</p>
            <a href="contact.php" class="cta-button">Contactar a Soporte</a>
        </div>
    </section>
    
    <script>
        // Script para acordeón de preguntas frecuentes
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);
                    
                    const answer = document.getElementById(this.getAttribute('aria-controls'));
                    answer.hidden = expanded;
                    
                    // Rotar ícono si es necesario
                });
            });
        });
    </script>
<?php require_once 'includes/footer.php'; ?>