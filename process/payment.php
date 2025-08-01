<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['reservation_id'])) {
    header('Location: ../routes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo = $_POST['metodo'];
    $datos = [];
    
    if ($metodo === 'transferencia') {
        // Manejar subida de comprobante
        if (isset($_FILES['comprobante'])) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES["comprobante"]["name"]);
            move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file);
            $datos['comprobante'] = $target_file;
        }
    } else {
        $datos['numero_tarjeta'] = $_POST['numero_tarjeta'];
        $datos['fecha_vencimiento'] = $_POST['fecha_vencimiento'];
        $datos['cvv'] = $_POST['cvv'];
    }
    
    $payment_id = processPayment($_SESSION['reservation_id'], $metodo, $datos);
    
    if ($payment_id) {
        $invoice_number = generateInvoice($_SESSION['reservation_id']);
        sendConfirmationEmail($_SESSION['passenger_id'], $_SESSION['reservation_id']);
        header('Location: ../confirmation.php');
        exit;
    } else {
        $_SESSION['error'] = "Error al procesar el pago";
        header('Location: ../payment.php');
        exit;
    }
}
?>