<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye PHPMailer (ajusta la ruta si usas Composer)
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db_connect.php';

function sendConfirmationEmail($passenger_id, $reservation_id) {
    global $pdo;
    // Obtiene datos del pasajero y reserva
    $stmt = $pdo->prepare("
        SELECT p.email, p.nombre, r.id AS reserva_id, r.fecha, r.hora, r.ruta, r.asiento
        FROM passengers p
        JOIN reservations r ON p.id = r.passenger_id
        WHERE p.id = ? AND r.id = ?
    ");
    $stmt->execute([$passenger_id, $reservation_id]);
    $data = $stmt->fetch();

    if (!$data) return false;

    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'espejocooperativa@gmail.com';
        $mail->Password = 'CooperativaEspejo123'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('no-reply@cooperativa.com', 'Cooperativa Espejo');
        $mail->addAddress($data['email'], $data['nombre']);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Reserva';
        $mail->Body = "
            <h2>¡Reserva confirmada!</h2>
            <p>Hola {$data['nombre']},</p>
            <p>Su reserva ha sido confirmada exitosamente. Aquí está el resumen:</p>
            <ul>
                <li><strong>Número de reserva:</strong> {$data['reserva_id']}</li>
                <li><strong>Ruta:</strong> {$data['ruta']}</li>
                <li><strong>Fecha:</strong> {$data['fecha']}</li>
                <li><strong>Hora:</strong> {$data['hora']}</li>
                <li><strong>Asiento:</strong> {$data['asiento']}</li>
            </ul>
            <p>Puede ver sus reservas aquí: 
                <a href='http://localhost/proyecto_cooperativa/my_reservations.php'>Ver reservas</a>
            </p>
            <p>Gracias por confiar en Cooperativa Espejo.</p>
        ";

        $mail->send();
        return true;
    } catch (\Exception $e) {
        echo "Error al enviar correo: " . $mail->ErrorInfo;
        return false;
    }
}
?>