<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reservation_id'], $data['new_date'], $data['new_time'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

require 'conexion.php'; // Tu archivo de conexión

$reservation_id = $data['reservation_id'];
$new_date = $data['new_date'];
$new_time = $data['new_time'];

$sql = "UPDATE reservas SET fecha = ?, hora = ? WHERE numero_reserva = ?";
$stmt = $conn->prepare($sql);
$success = $stmt->execute([$new_date, $new_time, $reservation_id]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
}
?>
