<?php 
session_start();
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

// Verificar que hay datos de pago exitoso
if (!isset($_SESSION['payment_success'])) {
    header('Location: routes.php');
    exit;
}

$payment_data = $_SESSION['payment_success'];
$reservation_id = $payment_data['reservation_id'];

// Obtener detalles completos de la reserva confirmada
$stmt = $pdo->prepare("
    SELECT r.id AS reserva_id, r.fecha_reserva, r.estado,
           p.nombre, p.apellido, p.cedula, p.email,
           ro.nombre AS ruta, ro.precio,
           s.hora, s.dias_semana, s.tipo_servicio,
           b.placa, b.modelo, b.tipo AS tipo_bus,
           COUNT(rs.id) as num_asientos,
           i.numero_factura, i.subtotal, i.iva, i.total,
           pay.metodo, pay.fecha_pago
    FROM reservations r
    JOIN passengers p ON r.passenger_id = p.id
    JOIN schedules s ON s.id = r.schedule_id
    JOIN routes ro ON s.ruta_id = ro.id
    JOIN buses b ON b.id = r.bus_id
    JOIN reservation_seats rs ON rs.reservation_id = r.id
    LEFT JOIN invoices i ON i.reservation_id = r.id
    LEFT JOIN payments pay ON pay.reservation_id = r.id
    WHERE r.id = ?
    GROUP BY r.id
");
$stmt->execute([$reservation_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener asientos reservados
$stmt_seats = $pdo->prepare("
    SELECT fila, posicion FROM reservation_seats
    WHERE reservation_id = ?
    ORDER BY fila, posicion
");
$stmt_seats->execute([$reservation_id]);
$asientos = $stmt_seats->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Clase para contenido solo para lectores de pantalla */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.confirmation-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.success-header {
    text-align: center;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 40px 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.success-icon {
    font-size: 4rem;
    margin-bottom: 15px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.success-header h1 {
    margin: 0 0 10px 0;
    font-size: 2.5rem;
}

.success-header p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.reservation-details {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.detail-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.detail-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.detail-section h3 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item strong {
    color: #495057;
    min-width: 120px;
}

.seats-display {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.seat-badge {
    background: #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9em;
    font-weight: 500;
}

.payment-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-top: 15px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    margin: 8px 0;
}

.payment-total {
    font-size: 1.3rem;
    font-weight: bold;
    color: #28a745;
    border-top: 2px solid #28a745;
    padding-top: 15px;
    margin-top: 15px;
}

.method-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 500;
    text-transform: uppercase;
}

.method-transferencia {
    background: #e3f2fd;
    color: #1565c0;
}

.method-tarjeta {
    background: #f3e5f5;
    color: #7b1fa2;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 15px 25px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #1e7e34;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-2px);
}

.important-info {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.important-info h4 {
    color: #856404;
    margin: 0 0 10px 0;
}

.important-info ul {
    margin: 0;
    padding-left: 20px;
}

.important-info li {
    margin: 5px 0;
    color: #856404;
}

@media (max-width: 768px) {
    .confirmation-container {
        padding: 10px;
    }
    
    .success-header {
        padding: 30px 15px;
    }
    
    .success-header h1 {
        font-size: 2rem;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
    
    .reservation-details {
        padding: 20px;
    }
}

.print-only {
    display: none;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block;
    }
    
    .confirmation-container {
        max-width: none;
        padding: 0;
    }
    
    .success-header {
        background: #f8f9fa !important;
        color: #333 !important;
        box-shadow: none;
    }
}
</style>

<div class="confirmation-container">
    <!-- Anuncio para lectores de pantalla -->
    <div role="status" aria-live="polite" class="sr-only">
        Pago exitoso. Tu reserva ha sido confirmada y procesada correctamente.
    </div>
    
    <!-- Encabezado de Éxito -->
    <header class="success-header" role="banner">
        <div class="success-icon" aria-hidden="true">✅</div>
        <h1 id="main-title">¡Pago Exitoso!</h1>
        <p>Tu reserva ha sido confirmada y procesada correctamente</p>
    </header>

    <?php if ($reserva): ?>
        <!-- Detalles de la Reserva -->
        <main class="reservation-details" role="main">
            <section class="detail-section" aria-labelledby="reservation-info">
                <h2 id="reservation-info">📋 Información de la Reserva</h2>
                <div class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <strong>Número de Reserva:</strong>
                            <span style="color: #007bff; font-weight: bold;" aria-label="Número de reserva">#<?= str_pad($reserva['reserva_id'], 6, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Fecha de Reserva:</strong>
                            <span><time datetime="<?= date('Y-m-d\TH:i', strtotime($reserva['fecha_reserva'])) ?>"><?= date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])) ?></time></span>
                        </div>
                        <div class="detail-item">
                            <strong>Estado:</strong>
                            <span style="color: #28a745; font-weight: bold; text-transform: uppercase;" aria-label="Estado de la reserva: confirmada"><?= htmlspecialchars($reserva['estado']) ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <strong>Número de Factura:</strong>
                            <span style="color: #6f42c1; font-weight: bold;" aria-label="Número de factura"><?= htmlspecialchars($reserva['numero_factura']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Fecha de Pago:</strong>
                            <span><time datetime="<?= date('Y-m-d\TH:i', strtotime($reserva['fecha_pago'])) ?>"><?= date('d/m/Y H:i', strtotime($reserva['fecha_pago'])) ?></time></span>
                        </div>
                        <div class="detail-item">
                            <strong>Método de Pago:</strong>
                            <span class="method-badge method-<?= $reserva['metodo'] ?>" aria-label="Método de pago: <?= $reserva['metodo'] === 'transferencia' ? 'Transferencia bancaria' : 'Tarjeta de crédito o débito' ?>">
                                <?= $reserva['metodo'] === 'transferencia' ? 'Transferencia' : 'Tarjeta' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="detail-section" aria-labelledby="passenger-info">
                <h2 id="passenger-info">👤 Información del Pasajero</h2>
                <div class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <strong>Nombre Completo:</strong>
                            <span><?= htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Cédula:</strong>
                            <span><?= htmlspecialchars($reserva['cedula']) ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <strong>Email:</strong>
                            <span><?= htmlspecialchars($reserva['email']) ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="detail-section" aria-labelledby="trip-details">
                <h2 id="trip-details">🚌 Detalles del Viaje</h2>
                <div class="detail-grid">
                    <div>
                        <div class="detail-item">
                            <strong>Ruta:</strong>
                            <span><?= htmlspecialchars($reserva['ruta']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Horario:</strong>
                            <span><?= htmlspecialchars($reserva['hora']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Días:</strong>
                            <span><?= htmlspecialchars($reserva['dias_semana']) ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <strong>Tipo de Servicio:</strong>
                            <span><?= htmlspecialchars($reserva['tipo_servicio']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Bus:</strong>
                            <span><?= htmlspecialchars($reserva['placa'] . ' - ' . $reserva['modelo']) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Tipo de Bus:</strong>
                            <span><?= htmlspecialchars($reserva['tipo_bus']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <strong>Asientos Reservados:</strong>
                    <div class="seats-display" role="list" aria-label="Lista de asientos reservados">
                        <?php 
                        $seat_map = ['ventana_izq'=>'A', 'pasillo_izq'=>'B', 'pasillo_der'=>'C', 'ventana_der'=>'D'];
                        foreach($asientos as $asiento): 
                        ?>
                            <span class="seat-badge" role="listitem" aria-label="Asiento fila <?= $asiento['fila'] ?> posición <?= $seat_map[$asiento['posicion']] ?? '?' ?>">
                                Fila <?= $asiento['fila'] ?><?= $seat_map[$asiento['posicion']] ?? '?' ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="detail-section" aria-labelledby="payment-summary">
                <h2 id="payment-summary">💰 Resumen de Pago</h2>
                <div class="payment-summary" role="table" aria-label="Desglose de costos del pago">
                    <div class="payment-row" role="row">
                        <span role="cell">Precio por asiento:</span>
                        <span role="cell">$<?= number_format($reserva['precio'], 2) ?></span>
                    </div>
                    <div class="payment-row" role="row">
                        <span role="cell">Cantidad de asientos:</span>
                        <span role="cell"><?= $reserva['num_asientos'] ?></span>
                    </div>
                    <div class="payment-row" role="row">
                        <span role="cell">Subtotal:</span>
                        <span role="cell">$<?= number_format($reserva['subtotal'], 2) ?></span>
                    </div>
                    <div class="payment-row" role="row">
                        <span role="cell">IVA (12%):</span>
                        <span role="cell">$<?= number_format($reserva['iva'], 2) ?></span>
                    </div>
                    <div class="payment-row payment-total" role="row">
                        <span role="cell">TOTAL PAGADO:</span>
                        <span role="cell" aria-label="Total pagado: <?= number_format($reserva['total'], 2) ?> dólares">$<?= number_format($reserva['total'], 2) ?></span>
                    </div>
                </div>
            </section>
        </main>

        <!-- Información Importante -->
        <aside class="important-info" role="complementary" aria-labelledby="important-info-title">
            <h3 id="important-info-title">📋 Información Importante:</h3>
            <ul>
                <li>Debes presentarte en la terminal 30 minutos antes de la hora de salida</li>
                <li>Lleva contigo tu cédula de identidad original</li>
                <li>Guarda este comprobante como constancia de tu reserva</li>
                <li>En caso de cancelación, comunícate con nosotros con al menos 24 horas de anticipación</li>
                <li>El equipaje de mano no debe exceder los 10kg</li>
            </ul>
        </aside>

        <!-- Botones de Acción -->
        <nav class="action-buttons no-print" role="navigation" aria-label="Acciones disponibles">
            <button onclick="window.print()" class="btn btn-success" aria-describedby="print-description">
                <span aria-hidden="true">📄</span> Imprimir Comprobante
            </button>
            <span id="print-description" class="sr-only">Imprime el comprobante de tu reserva</span>
            
            <a href="my_reservations.php" class="btn btn-secondary" aria-describedby="reservations-description">
                <span aria-hidden="true">📋</span> Ver Mis Reservas
            </a>
            <span id="reservations-description" class="sr-only">Ve a la página de todas tus reservas</span>
            
            <a href="index.php" class="btn btn-secondary" aria-describedby="home-description">
                <span aria-hidden="true">🏠</span> Volver al Inicio
            </a>
            <span id="home-description" class="sr-only">Regresa a la página principal</span>
        </nav>

        <!-- Información solo para impresión -->
        <div class="print-only">
            <hr style="margin: 30px 0;">
            <p style="text-align: center; color: #666; font-size: 0.9em;">
                Cooperativa de Transporte Intercantonal<br>
                Terminal Terrestre - Quito, Ecuador<br>
                Teléfono: (02) 123-4567 | Email: info@cooperativa.com<br>
                www.cooperativatransporte.com
            </p>
        </div>

    <?php else: ?>
        <main class="reservation-details" role="main">
            <div role="alert" aria-live="assertive" style="text-align: center; color: #dc3545; font-size: 1.2rem;">
                <span aria-hidden="true">❌</span> No se encontraron detalles de la reserva
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
            </div>
        </main>
    <?php endif; ?>
</div>

<script>
// Limpiar datos de sesión después de mostrar la confirmación
<?php 
// Limpiar datos temporales de la sesión pero mantener la sesión del usuario
unset($_SESSION['payment_success']);
unset($_SESSION['reservation_id']);
?>

// Auto-scroll al top
window.scrollTo(0, 0);

// Mensaje de confirmación adicional
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar un mensaje temporal de éxito
    setTimeout(function() {
        const successHeader = document.querySelector('.success-header');
        if (successHeader) {
            successHeader.style.transform = 'scale(1.02)';
            setTimeout(() => {
                successHeader.style.transform = 'scale(1)';
            }, 300);
        }
    }, 500);
});

// Prevenir el botón atrás del navegador para evitar reenvío
history.pushState(null, null, location.href);
window.onpopstate = function() {
    history.go(1);
};
</script>

<?php require_once 'includes/footer.php'; ?>