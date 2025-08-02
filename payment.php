<?php 
session_start();
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

// Validar que haya una reserva activa
if (!isset($_SESSION['reservation_id'])) {
    header('Location: routes.php');
    exit;
}

$reservation_id = $_SESSION['reservation_id'];

// Obtener detalles de la reserva
$stmt = $pdo->prepare("
    SELECT r.id AS reserva_id, r.fecha_reserva, r.estado,
           p.nombre, p.apellido,
           ro.nombre AS ruta,
           s.hora, s.dias_semana, s.tipo_servicio
    FROM reservations r
    JOIN passengers p ON r.passenger_id = p.id
    JOIN schedules s ON s.id = r.schedule_id
    JOIN routes ro ON s.ruta_id = ro.id
    JOIN buses b ON b.id = r.bus_id
    WHERE r.id = ?
");
$stmt->execute([$reservation_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener asientos reservados
$stmt_seats = $pdo->prepare("
    SELECT fila, posicion FROM reservation_seats
    WHERE reservation_id = ?
");
$stmt_seats->execute([$reservation_id]);
$asientos = $stmt_seats->fetchAll(PDO::FETCH_ASSOC);
?>

<section aria-labelledby="payment-title">
    <h1 id="payment-title">Proceso de Pago</h1>

    <h2>Resumen de la Reserva</h2>
    <?php if ($reserva): ?>
        <ul>
            <li><strong>Pasajero:</strong> <?= htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']) ?></li>
            <li><strong>Ruta:</strong> <?= htmlspecialchars($reserva['ruta']) ?></li>
            <li><strong>Hora:</strong> <?= htmlspecialchars($reserva['hora']) ?></li>
            <li><strong>Días:</strong> <?= htmlspecialchars($reserva['dias_semana']) ?></li>
            <li><strong>Servicio:</strong> <?= htmlspecialchars($reserva['tipo_servicio']) ?></li>
            <li><strong>Fecha de Reserva:</strong> <?= htmlspecialchars($reserva['fecha_reserva']) ?></li>
            <li><strong>Estado:</strong> <?= htmlspecialchars($reserva['estado']) ?></li>
            <li><strong>Asientos:</strong> 
                <?= implode(', ', array_map(function($a) {
                    $map = ['ventana_izq'=>'A', 'pasillo_izq'=>'B', 'pasillo_der'=>'C', 'ventana_der'=>'D'];
                    return $a['fila'] . ($map[$a['posicion']] ?? '?');
                }, $asientos)); ?>
            </li>
        </ul>
    <?php else: ?>
        <p>No se encontró información de la reserva.</p>
    <?php endif; ?>

    <div class="payment-options">
        <button id="btn-transferencia" class="payment-method active" aria-pressed="true">Transferencia Bancaria</button>
        <button id="btn-tarjeta" class="payment-method" aria-pressed="false">Tarjeta de Crédito/Débito</button>
    </div>

    <form id="payment-form" action="process/payment.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="metodo" id="payment-method" value="transferencia">
        
        <div id="transferencia-form" class="payment-form">
            <div class="form-group">
                <label for="comprobante">Comprobante de Transferencia*</label>
                <input type="file" id="comprobante" name="comprobante" accept="image/*,.pdf" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="referencia">Número de Referencia*</label>
                <input type="text" id="referencia" name="referencia" required>
            </div>
        </div>
        
        <div id="tarjeta-form" class="payment-form" style="display:none;">
            <div class="form-group">
                <label for="numero_tarjeta">Número de Tarjeta*</label>
                <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="1234 5678 9012 3456">
            </div>
            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento*</label>
                <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" placeholder="MM/AA">
            </div>
            <div class="form-group">
                <label for="cvv">CVV*</label>
                <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
            </div>
        </div>

        <button type="submit">Realizar Pago</button>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnTransferencia = document.getElementById('btn-transferencia');
        const btnTarjeta = document.getElementById('btn-tarjeta');
        const formTransferencia = document.getElementById('transferencia-form');
        const formTarjeta = document.getElementById('tarjeta-form');
        const paymentMethod = document.getElementById('payment-method');
        
        btnTransferencia.addEventListener('click', function() {
            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');
            btnTarjeta.classList.remove('active');
            btnTarjeta.setAttribute('aria-pressed', 'false');
            formTransferencia.style.display = 'block';
            formTarjeta.style.display = 'none';
            paymentMethod.value = 'transferencia';
        });

        btnTarjeta.addEventListener('click', function() {
            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');
            btnTransferencia.classList.remove('active');
            btnTransferencia.setAttribute('aria-pressed', 'false');
            formTransferencia.style.display = 'none';
            formTarjeta.style.display = 'block';
            paymentMethod.value = 'tarjeta';
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
