<?php 
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['reservation_id'])) {
    header('Location: routes.php');
    exit;
}

$reservation_id = $_SESSION['reservation_id'];
?>
    <section aria-labelledby="invoice-title">
        <h1 id="invoice-title">Factura de Reserva</h1>
        
        <div class="invoice-details">
            <?php
            // En un sistema real, recuperaríamos estos datos de la base de datos
            $numero_factura = 'FAC-' . str_pad($reservation_id, 8, '0', STR_PAD_LEFT);
            $fecha_emision = date('d/m/Y');
            $subtotal = 100.00;
            $iva = $subtotal * 0.12;
            $total = $subtotal + $iva;
            ?>
            
            <p><strong>Número de Factura:</strong> <?= $numero_factura ?></p>
            <p><strong>Fecha de Emisión:</strong> <?= $fecha_emision ?></p>
            <p><strong>Subtotal:</strong> $<?= number_format($subtotal, 2) ?></p>
            <p><strong>IVA (12%):</strong> $<?= number_format($iva, 2) ?></p>
            <p><strong>Total:</strong> $<?= number_format($total, 2) ?></p>
            
            <button id="print-invoice" aria-label="Imprimir factura">Imprimir Factura</button>
            <button id="download-invoice" aria-label="Descargar factura">Descargar PDF</button>
        </div>
    </section>
<?php require_once 'includes/footer.php'; ?>