<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['passenger_id'])) {
    header('Location: ../login.php');
    exit;
}

// Solo permite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_id = $_SESSION['passenger_id'];
    $schedule_id = $_POST['schedule_id'] ?? null;
    $bus_id = $_POST['bus_id'] ?? null;
    $ruta_id = $_POST['ruta_id'] ?? null;
    $asientos = $_POST['asientos'] ?? [];
    $estado = 'pendiente'; // ✅ Debe coincidir con el ENUM de la tabla

    // Validaciones básicas
    if (empty($schedule_id) || empty($bus_id) || empty($asientos)) {
        $_SESSION['error'] = "Datos incompletos para realizar la reserva.";
        header("Location: ../seats.php?schedule_id=$schedule_id&bus_id=$bus_id");
        exit;
    }

    if (count($asientos) > 40) {
        $_SESSION['error'] = "No puede reservar más de 40 asientos.";
        header("Location: ../seats.php?schedule_id=$schedule_id&bus_id=$bus_id");
        exit;
    }

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar en tabla reservations
        $stmt = $pdo->prepare("INSERT INTO reservations (passenger_id, schedule_id, bus_id, estado) VALUES (?, ?, ?, ?)");
        $stmt->execute([$passenger_id, $schedule_id, $bus_id, $estado]);
        $reservation_id = $pdo->lastInsertId();

        // Preparar inserción de asientos
        $stmt_asiento = $pdo->prepare("INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion) VALUES (?, ?, ?, ?)");

        foreach ($asientos as $asiento) {
            $parts = explode('_', $asiento);
            $fila = $parts[0];
            $posicion = implode('_', array_slice($parts, 1));

            // Validar que el valor de posición sea uno de los válidos
            $valid_posiciones = ['ventana_izq', 'ventana_der', 'pasillo_izq', 'pasillo_der'];
            if (!in_array($posicion, $valid_posiciones)) {
                throw new Exception("Posición inválida: $posicion");
            }

            $stmt_asiento->execute([$reservation_id, $bus_id, $fila, $posicion]);
        }

        // Confirmar la transacción
        $pdo->commit();

        $_SESSION['reservation_id'] = $reservation_id;
        header('Location: ../payment.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al crear la reserva: " . $e->getMessage();
        header("Location: ../seats.php?schedule_id=$schedule_id&bus_id=$bus_id");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error en los datos: " . $e->getMessage();
        header("Location: ../seats.php?schedule_id=$schedule_id&bus_id=$bus_id");
        exit;
    }
} else {
    // Si no es POST, redirecciona
    header('Location: ../routes.php');
    exit;
}
