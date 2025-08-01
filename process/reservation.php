<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['passenger_id'])) {
    header('Location: ../register.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = $_POST['schedule_id'];
    $bus_id = $_POST['bus_id'];
    $asientos = $_POST['asientos'] ?? [];
    
    if (count($asientos) > 40) {
        $_SESSION['error'] = "No puede reservar más de 40 asientos";
        header('Location: ../seats.php?schedule_id=' . $schedule_id . '&bus_id=' . $bus_id);
        exit;
    }
    
    $reservation_id = createReservation($_SESSION['passenger_id'], $schedule_id, $bus_id, $asientos);
    
    if ($reservation_id) {
        $_SESSION['reservation_id'] = $reservation_id;
        header('Location: ../payment.php');
        exit;
    } else {
        $_SESSION['error'] = "Error al crear la reserva";
        header('Location: ../seats.php?schedule_id=' . $schedule_id . '&bus_id=' . $bus_id);
        exit;
    }
}
?>