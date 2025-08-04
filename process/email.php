<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db_connect.php';

function sendConfirmationEmail($passenger_id, $reservation_id) {
    global $pdo;
    // Consulta correcta según la estructura de la base de datos
    $stmt = $pdo->prepare("
        SELECT 
            p.email, 
            p.nombre, 
            r.id AS reserva_id, 
            rs.hora, 
            rt.nombre AS ruta, 
            b.placa, 
            rs.dias_semana,
            r.estado,
            s.fila,
            s.posicion,
            r.fecha_reserva,
            r.bus_id,
            r.schedule_id,
            r.passenger_id,
            b.modelo,
            b.tipo,
            r.estado,
            r.fecha_reserva,
            r.id AS reserva_id,
            s.id AS asiento_id
        FROM reservations r
        JOIN passengers p ON p.id = r.passenger_id
        JOIN schedules rs ON rs.id = r.schedule_id
        JOIN routes rt ON rt.id = rs.ruta_id
        JOIN buses b ON b.id = r.bus_id
        LEFT JOIN reservation_seats s ON s.reservation_id = r.id
        WHERE r.id = ? AND p.id = ?
        LIMIT 1
    ");
    $stmt->execute([$reservation_id, $passenger_id]);
    $data = $stmt->fetch();

    if (!$data) return false;

    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'espejocooperativa@gmail.com';
        $mail->Password = 'ebnw jlsm psqz lyoy'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('espejocooperativa@gmail.com', 'Cooperativa Espejo');
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
                <li><strong>Hora:</strong> {$data['hora']}</li>
                <li><strong>Día(s):</strong> {$data['dias_semana']}</li>
                <li><strong>Fecha de reserva:</strong> {$data['fecha_reserva']}</li>
                <li><strong>Bus:</strong> {$data['placa']} ({$data['modelo']} - {$data['tipo']})</li>
                <li><strong>Asiento:</strong> Fila {$data['fila']} - {$data['posicion']}</li>
                <li><strong>Estado:</strong> {$data['estado']}</li>
            </ul>
            <p>Puede ver sus reservas aquí: 
                <a href='http://localhost/Proyecto-Cooperativa/my_reservations.php'>Ver reservas</a>
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