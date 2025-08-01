<?php 
$pageTitle = "Centro de Ayuda - Cooperativa de Transporte Espejo";
require_once 'includes/header.php'; 
?>

<main class="help-page">
    <div class="container">
        <nav aria-label="Migas de pan" class="breadcrumb">
            <ol>
                <li><a href="index.php">Inicio</a></li>
                <li aria-current="page">Ayuda</li>
            </ol>
        </nav>
        
        <section aria-labelledby="help-title" class="help-section">
            <header class="section-header">
                <h1 id="help-title">Centro de Ayuda</h1>
                <p class="intro-text">Encuentre respuestas a las preguntas más frecuentes y guías para usar nuestro sistema.</p>
            </header>
            
            <div class="help-content">
                <section class="faq-section" aria-labelledby="faq-title">
                    <h2 id="faq-title" class="faq-heading">Preguntas Frecuentes</h2>
                    
                    <div class="faq-accordion">
                        <!-- Pregunta 1 -->
                        <div class="faq-item">
                            <h3>
                                <button class="faq-question" aria-expanded="false" aria-controls="faq1">
                                    <span class="question-text">¿Cómo puedo reservar un asiento?</span>
                                    <span class="accordion-icon" aria-hidden="true"></span>
                                </button>
                            </h3>
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
                        
                        <!-- Pregunta 2 -->
                        <div class="faq-item">
                            <h3>
                                <button class="faq-question" aria-expanded="false" aria-controls="faq2">
                                    <span class="question-text">¿Cuántos asientos puedo reservar?</span>
                                    <span class="accordion-icon" aria-hidden="true"></span>
                                </button>
                            </h3>
                            <div class="faq-answer" id="faq2" hidden>
                                <p>Puede reservar hasta 10 asientos por transacción. Para grupos mayores, contáctenos a través de nuestro formulario de contacto.</p>
                            </div>
                        </div>
                        
                        <!-- Pregunta 3 -->
                        <div class="faq-item">
                            <h3>
                                <button class="faq-question" aria-expanded="false" aria-controls="faq3">
                                    <span class="question-text">¿Puedo cancelar o modificar mi reserva?</span>
                                    <span class="accordion-icon" aria-hidden="true"></span>
                                </button>
                            </h3>
                            <div class="faq-answer" id="faq3" hidden>
                                <p>Sí, puede cancelar o modificar su reserva hasta 24 horas antes de la salida del bus:</p>
                                <ul>
                                    <li>Para modificaciones: acceda a "Mis Reservas" y seleccione la opción "Modificar"</li>
                                    <li>Para cancelaciones: acceda a "Mis Reservas" y seleccione "Cancelar"</li>
                                </ul>
                                <p>Las cancelaciones están sujetas a nuestra política de reembolsos.</p>
                            </div>
                        </div>
                        
                        <!-- Pregunta 4 -->
                        <div class="faq-item">
                            <h3>
                                <button class="faq-question" aria-expanded="false" aria-controls="faq4">
                                    <span class="question-text">¿Qué métodos de pago están disponibles?</span>
                                    <span class="accordion-icon" aria-hidden="true"></span>
                                </button>
                            </h3>
                            <div class="faq-answer" id="faq4" hidden>
                                <p>Aceptamos los siguientes métodos de pago:</p>
                                <ul>
                                    <li>Tarjetas de crédito/débito (Visa, Mastercard, Diners)</li>
                                    <li>Transferencias bancarias</li>
                                    <li>Pago en efectivo en nuestras oficinas</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Pregunta 5 -->
                        <div class="faq-item">
                            <h3>
                                <button class="faq-question" aria-expanded="false" aria-controls="faq5">
                                    <span class="question-text">¿Qué debo presentar el día del viaje?</span>
                                    <span class="accordion-icon" aria-hidden="true"></span>
                                </button>
                            </h3>
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
                </section>
                
                <section class="support-section" aria-labelledby="support-title">
                    <h2 id="support-title" class="support-heading">Soporte Adicional</h2>
                    <p>Si no encontró respuesta a su pregunta, contáctenos directamente:</p>
                    <a href="contact.php" class="cta-button">Contactar a Soporte</a>
                </section>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            
            const answer = document.getElementById(this.getAttribute('aria-controls'));
            answer.hidden = expanded;
            
            // Actualizar ícono
            const icon = this.querySelector('.accordion-icon');
            icon.style.transform = expanded ? 'rotate(0deg)' : 'rotate(180deg)';
        });
        
        // Permitir navegación por teclado
        question.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>