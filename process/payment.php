<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Validar que haya una reserva activa
if (!isset($_SESSION['reservation_id'])) {
    $_SESSION['error'] = "No hay una reserva activa";
    header('Location: ../routes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $reservation_id = $_SESSION['reservation_id'];
        $metodo = $_POST['metodo'];
        
        // Obtener datos de la reserva para calcular el monto
        $stmt = $pdo->prepare("
            SELECT r.id, ro.precio, COUNT(rs.id) as num_asientos
            FROM reservations r
            JOIN schedules s ON r.schedule_id = s.id
            JOIN routes ro ON s.ruta_id = ro.id
            JOIN reservation_seats rs ON rs.reservation_id = r.id
            WHERE r.id = ?
            GROUP BY r.id
        ");
        $stmt->execute([$reservation_id]);
        $reserva_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reserva_data) {
            throw new Exception("No se encontró la reserva");
        }
        
        $monto = $reserva_data['precio'] * $reserva_data['num_asientos'];
        $datos_pago = [];
        
        // Procesar según método de pago
        if ($metodo === 'transferencia') {
            // Validar campos requeridos
            if (empty($_POST['referencia'])) {
                throw new Exception("El número de referencia es requerido");
            }
            
            $comprobante_path = null;
            
            // Manejar subida de comprobante
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                $file_type = $_FILES['comprobante']['type'];
                
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y PDF");
                }
                
                $max_size = 5 * 1024 * 1024; // 5MB
                if ($_FILES['comprobante']['size'] > $max_size) {
                    throw new Exception("El archivo es demasiado grande. Máximo 5MB");
                }
                
                $upload_dir = "../uploads/comprobantes/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES["comprobante"]["name"], PATHINFO_EXTENSION);
                $new_filename = "comprobante_" . $reservation_id . "_" . time() . "." . $file_extension;
                $target_file = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
                    $comprobante_path = $target_file;
                } else {
                    throw new Exception("Error al subir el comprobante");
                }
            } else {
                throw new Exception("El comprobante es requerido para transferencias");
            }
            
            $datos_pago = [
                'referencia' => $_POST['referencia'],
                'comprobante' => $comprobante_path
            ];
            
        } else if ($metodo === 'tarjeta') {
            // Validar campos de tarjeta
            if (empty($_POST['numero_tarjeta']) || empty($_POST['fecha_vencimiento']) || empty($_POST['cvv'])) {
                throw new Exception("Todos los campos de la tarjeta son requeridos");
            }
            
            // Validaciones básicas
            $numero_tarjeta = preg_replace('/\s+/', '', $_POST['numero_tarjeta']);
            if (!preg_match('/^\d{16}$/', $numero_tarjeta)) {
                throw new Exception("Número de tarjeta inválido");
            }
            
            if (!preg_match('/^\d{2}\/\d{2}$/', $_POST['fecha_vencimiento'])) {
                throw new Exception("Fecha de vencimiento inválida (MM/AA)");
            }
            
            if (!preg_match('/^\d{3}$/', $_POST['cvv'])) {
                throw new Exception("CVV inválido");
            }
            
            // Simular procesamiento de tarjeta (en producción se integraría con pasarela de pago)
            $datos_pago = [
                'numero_tarjeta' => 'XXXX-XXXX-XXXX-' . substr($numero_tarjeta, -4),
                'fecha_vencimiento' => $_POST['fecha_vencimiento'],
                'tipo_tarjeta' => determinarTipoTarjeta($numero_tarjeta)
            ];
        } else {
            throw new Exception("Método de pago no válido");
        }
        
        // Insertar pago en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO payments (reservation_id, metodo, monto, comprobante, datos_tarjeta, estado)
            VALUES (?, ?, ?, ?, ?, 'completado')
        ");
        
        $comprobante = ($metodo === 'transferencia') ? $datos_pago['comprobante'] : null;
        $datos_tarjeta = ($metodo === 'tarjeta') ? json_encode($datos_pago) : null;
        
        $stmt->execute([
            $reservation_id,
            $metodo,
            $monto,
            $comprobante,
            $datos_tarjeta
        ]);
        
        $payment_id = $pdo->lastInsertId();
        
        // Actualizar estado de la reserva
        $stmt = $pdo->prepare("UPDATE reservations SET estado = 'pagado' WHERE id = ?");
        $stmt->execute([$reservation_id]);
        
        // Generar factura
        $numero_factura = generarNumeroFactura();
        $subtotal = $monto;
        $iva = $subtotal * 0.12; // 12% IVA en Ecuador
        $total = $subtotal + $iva;
        
        $stmt = $pdo->prepare("
            INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$reservation_id, $numero_factura, $subtotal, $iva, $total]);
        
        // Enviar email de confirmación (si la función existe)
        if (function_exists('enviarEmailConfirmacion')) {
            $passenger_id = $_SESSION['passenger_id'] ?? null;
            if ($passenger_id) {
                enviarEmailConfirmacion($passenger_id, $reservation_id);
            }
        }
        
        $pdo->commit();
        
        // Guardar datos para la confirmación
        $_SESSION['payment_success'] = [
            'reservation_id' => $reservation_id,
            'payment_id' => $payment_id,
            'numero_factura' => $numero_factura,
            'metodo' => $metodo,
            'monto' => $total
        ];
        
        $_SESSION['success'] = "¡Pago procesado exitosamente!";
        header('Location: ../confirmation.php');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../payment.php');
        exit;
    }
} else {
    header('Location: ../payment.php');
    exit;
}

function determinarTipoTarjeta($numero) {
    $numero = preg_replace('/\s+/', '', $numero);
    
    if (preg_match('/^4/', $numero)) {
        return 'Visa';
    } elseif (preg_match('/^5[1-5]/', $numero)) {
        return 'MasterCard';
    } elseif (preg_match('/^3[47]/', $numero)) {
        return 'American Express';
    } else {
        return 'Desconocida';
    }
}

function generarNumeroFactura() {
    // Formato: FAC-YYYY-NNNNNN
    $year = date('Y');
    $numero = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    return "FAC-{$year}-{$numero}";
}
?>