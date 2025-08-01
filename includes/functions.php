<?php
require_once 'db_connect.php';

function getRoutes() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM routes");
    return $stmt->fetchAll();
}

function getSchedules($ruta_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE ruta_id = ?");
    $stmt->execute([$ruta_id]);
    return $stmt->fetchAll();
}

function getBuses($ruta_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM buses WHERE ruta_id = ?");
    $stmt->execute([$ruta_id]);
    return $stmt->fetchAll();
}

function createReservation($passenger_id, $schedule_id, $bus_id, $asientos) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Crear reserva
        $stmt = $pdo->prepare("INSERT INTO reservations (passenger_id, schedule_id, bus_id) VALUES (?, ?, ?)");
        $stmt->execute([$passenger_id, $schedule_id, $bus_id]);
        $reservation_id = $pdo->lastInsertId();
        
        // Registrar asientos
        foreach ($asientos as $asiento) {
            list($fila, $posicion) = explode('_', $asiento);
            $stmt = $pdo->prepare("INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion) VALUES (?, ?, ?, ?)");
            $stmt->execute([$reservation_id, $bus_id, $fila, $posicion]);
        }
        
        $pdo->commit();
        return $reservation_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function processPayment($reservation_id, $metodo, $datos) {
    global $pdo;
    
    // Obtener total de reserva
    $stmt = $pdo->prepare("
        SELECT COUNT(rs.id) * r.precio AS total 
        FROM reservation_seats rs
        JOIN reservations res ON rs.reservation_id = res.id
        JOIN buses b ON rs.bus_id = b.id
        JOIN routes r ON b.ruta_id = r.id
        WHERE res.id = ?
    ");
    $stmt->execute([$reservation_id]);
    $total = $stmt->fetchColumn();
    
    $comprobante = null;
    $datos_tarjeta = null;
    
    if ($metodo === 'transferencia') {
        $comprobante = $datos['comprobante'] ?? null;
    } else {
        $datos_tarjeta = json_encode([
            'numero' => $datos['numero_tarjeta'],
            'vencimiento' => $datos['fecha_vencimiento'],
            'cvv' => $datos['cvv']
        ]);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (reservation_id, metodo, monto, comprobante, datos_tarjeta, estado) 
        VALUES (?, ?, ?, ?, ?, 'completado')
    ");
    $stmt->execute([$reservation_id, $metodo, $total, $comprobante, $datos_tarjeta]);
    
    // Actualizar estado de reserva
    $stmt = $pdo->prepare("UPDATE reservations SET estado = 'pagado' WHERE id = ?");
    $stmt->execute([$reservation_id]);
    
    return $pdo->lastInsertId();
}

function generateInvoice($reservation_id) {
    global $pdo;
    
    // Obtener datos para factura
    $stmt = $pdo->prepare("
        SELECT p.*, res.id AS reserva_id, r.nombre AS ruta, 
               COUNT(rs.id) AS asientos, SUM(rt.precio) AS subtotal
        FROM passengers p
        JOIN reservations res ON p.id = res.passenger_id
        JOIN reservation_seats rs ON res.id = rs.reservation_id
        JOIN buses b ON rs.bus_id = b.id
        JOIN routes rt ON b.ruta_id = rt.id
        WHERE res.id = ?
    ");
    $stmt->execute([$reservation_id]);
    $data = $stmt->fetch();
    
    $subtotal = $data['subtotal'];
    $iva = $subtotal * 0.12;
    $total = $subtotal + $iva;
    $numero_factura = 'FAC-' . str_pad($reservation_id, 8, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("
        INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$reservation_id, $numero_factura, $subtotal, $iva, $total]);
    
    return $numero_factura;
}

function sendConfirmationEmail($passenger_id, $reservation_id) {
    // Simulación de envío de correo
    return true;
}
?>