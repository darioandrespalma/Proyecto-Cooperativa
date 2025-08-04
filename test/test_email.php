<?php
require_once '../includes/db_connect.php';
require_once '../process/email.php';

// Usa IDs válidos de tu base de datos
$passenger_id = 1;
$reservation_id = 1;

if (sendConfirmationEmail($passenger_id, $reservation_id)) {
    echo "Correo enviado correctamente.";
} else {
    echo "Error al enviar el correo.";
}
?>