<?php 
session_start();
require_once 'includes/header.php';

if (!isset($_SESSION['reservation_id'])) {
    header('Location: routes.php');
    exit;
}
?>
    <section aria-labelledby="confirmation-title">
        <h1 id="confirmation-title">¡Reserva Confirmada!</h1>
        
        <div class="confirmation-message">
            <p>Su reserva ha sido confirmada exitosamente. Se ha enviado un correo electrónico con los detalles de su reserva y factura.</p>
            <p>Número de reserva: <strong><?= $_SESSION['reservation_id'] ?></strong></p>
            
            <div class="actions">
                <a href="invoice.php" class="cta-button">Ver Factura</a>
                <a href="index.php" class="cta-button secondary">Volver al Inicio</a>
            </div>
        </div>
    </section>
<?php 
// Limpiar sesión después de confirmación
unset($_SESSION['passenger_id']);
unset($_SESSION['reservation_id']);
require_once 'includes/footer.php'; 
?>