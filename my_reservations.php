<?php 
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

$reservas = [];
$cedula_busqueda = '';
$mensaje = '';

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula_busqueda = trim($_POST['cedula']);
    
    // Nueva validación: solo verificar que sea un número de 10 dígitos
    if (!preg_match('/^[0-9]{10}$/', $cedula_busqueda)) {
        $mensaje = "La cédula debe ser un número de 10 dígitos.";
    } else {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si existe el pasajero con esa cédula
            $check_passenger = $pdo->prepare("SELECT id, nombre, apellido FROM passengers WHERE cedula = ?");
            $check_passenger->execute([$cedula_busqueda]);
            $passenger = $check_passenger->fetch(PDO::FETCH_ASSOC);
            
            if (!$passenger) {
                $mensaje = "No se encontró ningún pasajero registrado con la cédula: " . htmlspecialchars($cedula_busqueda);
            } else {
                // El pasajero existe, ahora buscar sus reservas usando LEFT JOIN para no perder datos
                $stmt = $pdo->prepare("
                    SELECT 
                        r.id AS numero_reserva,
                        r.fecha_reserva,
                        DATE(r.fecha_reserva) AS fecha_viaje,
                        r.estado AS estado_reserva,
                        s.ruta_id,
                        rt.nombre AS ruta,
                        s.hora AS hora_viaje,
                        s.tipo_servicio,
                        s.dias_semana,
                        b.placa AS bus_placa,
                        b.tipo AS tipo_bus,
                        i.total AS precio_total,
                        p.estado AS estado_pago,
                        rs.asientos,
                        i.numero_factura,
                        pa.nombre,
                        pa.apellido,
                        pa.email,
                        pa.cedula
                    FROM reservations r
                    LEFT JOIN schedules s ON r.schedule_id = s.id
                    LEFT JOIN routes rt ON s.ruta_id = rt.id
                    LEFT JOIN buses b ON r.bus_id = b.id
                    LEFT JOIN passengers pa ON r.passenger_id = pa.id
                    LEFT JOIN invoices i ON r.id = i.reservation_id
                    LEFT JOIN payments p ON r.id = p.reservation_id
                    LEFT JOIN (
                        SELECT 
                            reservation_id, 
                            GROUP_CONCAT(
                                CONCAT(
                                    fila, 
                                    CASE posicion
                                        WHEN 'ventana_izq' THEN 'A'
                                        WHEN 'pasillo_izq' THEN 'B'
                                        WHEN 'pasillo_der' THEN 'C'
                                        WHEN 'ventana_der' THEN 'D'
                                        ELSE ''
                                    END
                                ) SEPARATOR ', '
                            ) AS asientos
                        FROM reservation_seats
                        GROUP BY reservation_id
                    ) rs ON r.id = rs.reservation_id
                    WHERE pa.cedula = ?
                    ORDER BY r.fecha_reserva DESC
                ");
                
                $stmt->execute([$cedula_busqueda]);
                $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($reservas)) {
                    $mensaje = "El pasajero " . htmlspecialchars($passenger['nombre'] . " " . $passenger['apellido']) . " no tiene reservas registradas.";
                } else {
                    $mensaje = "Se encontraron " . count($reservas) . " reserva(s) para " . htmlspecialchars($passenger['nombre'] . " " . $passenger['apellido']);
                }
            }
            
        } catch (PDOException $e) {
            $mensaje = "Ocurrió un error en la base de datos. Detalle: " . $e->getMessage();
        }
    }
}
?>

<section aria-labelledby="reservations-title">
    <div class="breadcrumb">
        <a href="index.php">Inicio</a> > Mis Reservas
    </div>
    
    <h1 id="reservations-title">Mis Reservas</h1>
    <p class="intro">Consulte y gestione sus reservas ingresando su número de cédula.</p>
    
    <!-- Formulario de búsqueda -->
    <div class="search-container">
        <form method="post" class="search-form" id="reservation-search">
            <div class="form-group">
                <label for="cedula">Número de Cédula*</label>
                <input 
                    type="text" 
                    id="cedula" 
                    name="cedula" 
                    value="<?= htmlspecialchars($cedula_busqueda) ?>"
                    required 
                    aria-required="true"
                    placeholder="Ej: 1234567890"
                    pattern="[0-9]{10}"
                    title="Ingrese un número de cédula válido (10 dígitos)"
                    maxlength="10"
                >
                <div class="error-message" aria-live="polite"></div>
            </div>
            <button type="submit" class="search-button">
                <span class="button-icon">🔍</span>
                Buscar Reservas
            </button>
        </form>
    </div>
    
    <!-- Mensaje de estado -->
    <?php if (!empty($mensaje)): ?>
        <div class="message-container">
            <div class="alert <?= empty($reservas) ? 'alert-warning' : 'alert-info' ?>" role="alert">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Resultados de reservas -->
    <?php if (!empty($reservas)): ?>
        <div class="reservations-container">
            <?php foreach ($reservas as $reserva): ?>
                <article class="reservation-card" aria-labelledby="reserva-<?= $reserva['numero_reserva'] ?>">
                    <header class="reservation-header">
                        <h3 id="reserva-<?= $reserva['numero_reserva'] ?>">
                            Reserva #<?= str_pad($reserva['numero_reserva'], 6, '0', STR_PAD_LEFT) ?>
                        </h3>
                        <div class="reservation-status" style="display: flex; gap: 8px; align-items: center;">
                            <span class="status-badge status-<?= strtolower($reserva['estado_reserva']) ?>">
                               Estado Reserva: <?= ucfirst($reserva['estado_reserva']) ?>
                            </span>
                            <span class="payment-badge payment-<?= strtolower($reserva['estado_pago']) ?>">
                               Estado Pago: <?= ucfirst($reserva['estado_pago']) ?>
                            </span>
                        </div>
                    </header>
                    
                    <div class="reservation-details">
                        <div class="detail-section">
                            <h4>🚌 Información del Viaje</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Ruta:</span>
                                    <span class="detail-value"><?= htmlspecialchars($reserva['ruta'] ?? 'N/A') ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Fecha de Viaje:</span>
                                    <span class="detail-value">
                                        <?= $reserva['fecha_viaje'] ? date('d/m/Y', strtotime($reserva['fecha_viaje'])) : 'N/A' ?>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Hora de Salida:</span>
                                    <span class="detail-value">
                                        <?= $reserva['hora_viaje'] ? date('H:i', strtotime($reserva['hora_viaje'])) : 'N/A' ?>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Bus:</span>
                                    <span class="detail-value"><?= htmlspecialchars(($reserva['bus_placa'] ?? 'N/A') . ' (' . ($reserva['tipo_bus'] ?? 'N/A') . ')') ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Asientos:</span>
                                    <span class="detail-value seats-list">
                                        <?= htmlspecialchars($reserva['asientos'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Precio Total:</span>
                                    <span class="detail-value price-total">$<?= number_format($reserva['precio_total'] ?? 0, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>👤 Información del Pasajero</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Nombre:</span>
                                    <span class="detail-value"><?= htmlspecialchars(($reserva['nombre'] ?? 'N/A') . ' ' . ($reserva['apellido'] ?? '')) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Cédula:</span>
                                    <span class="detail-value"><?= htmlspecialchars($reserva['cedula'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones de la reserva -->
                    <footer class="reservation-actions">
                        <div class="action-buttons">
                            <?php 
                            // Calcula si la reserva es futura y si la hora de viaje es al menos 1 hora en el futuro.
                            $fecha_viaje = strtotime(($reserva['fecha_viaje'] ?? '') . ' ' . ($reserva['hora_viaje'] ?? ''));
                            $es_futura = $fecha_viaje > time();
                            // El cambio aquí: la cancelación es posible hasta 1 hora antes del viaje (1*3600 segundos).
                            $puede_cancelar = $fecha_viaje > (time() + 1*3600); 
                            ?>
                            
                            <?php if ($es_futura && strtolower($reserva['estado_reserva'] ?? '') !== 'cancelado'): ?>
                                <button class="action-btn btn-details" onclick="toggleDetails(<?= $reserva['numero_reserva'] ?>)" aria-expanded="false" aria-controls="expanded-details-<?= $reserva['numero_reserva'] ?>">
                                    <span class="btn-icon">👁️</span>
                                    Ver Detalles
                                </button>
                                
                                <?php if ($puede_cancelar): ?>
                                <button class="action-btn btn-modify" onclick="openModifyModal(<?= $reserva['numero_reserva'] ?>)">
                                    <span class="btn-icon">✏️</span>
                                    Modificar
                                </button>
                                <button class="action-btn btn-cancel" onclick="openCancelModal(<?= $reserva['numero_reserva'] ?>)">
                                    <span class="btn-icon">❌</span>
                                    Cancelar
                                </button>
                                <?php else: ?>
                                <span class="action-disabled">
                                    <span class="btn-icon">🚫</span>
                                    No se puede modificar/cancelar (menos de 1h)
                                </span>
                                <?php endif; ?>

                                <?php if ($reserva['numero_factura']): ?>
                                <a href="download_invoice.php?reservation_id=<?= $reserva['numero_reserva'] ?>" class="action-btn btn-download">
                                    <span class="btn-icon">📄</span>
                                    Factura
                                </a>
                                <?php endif; ?>
                            
                            <?php else: ?>
                                <span class="status-cancelled">
                                    <?= strtolower($reserva['estado_reserva'] ?? '') === 'cancelado' ? 'Reserva Cancelada' : 'Viaje Finalizado' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </footer>
                    
                    <!-- Detalles expandibles -->
                    <div id="expanded-details-<?= $reserva['numero_reserva'] ?>" class="reservation-expanded-details" style="display: none;">
                        <div class="expanded-content">
                            <h4>📋 Detalles Adicionales</h4>
                            <div class="additional-info">
                                <p><strong>Fecha de Reserva:</strong> <?= $reserva['fecha_reserva'] ? date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])) : 'N/A' ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($reserva['email'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Sección de ayuda -->
    <div class="help-section">
        <h2>¿Necesita ayuda?</h2>
        <p>Si tiene problemas para encontrar su reserva o necesita asistencia, puede:</p>
        <div class="help-actions">
            <a href="contact.php" class="help-btn">
                <span class="btn-icon">📞</span>
                Contactar Soporte
            </a>
            <a href="help.php" class="help-btn">
                <span class="btn-icon">❓</span>
                Centro de Ayuda
            </a>
        </div>
    </div>
</section>

<!-- Modales -->
<div id="modify-modal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="modify-title">
    <div class="modal-content">
        <header class="modal-header">
            <h3 id="modify-title">Modificar Reserva</h3>
            <button class="modal-close" onclick="closeModal('modify-modal')" aria-label="Cerrar modal">×</button>
        </header>
        <div class="modal-body">
            <p>Para modificar su reserva, será redirigido al proceso de selección de nuevos asientos.</p>
            <p><strong>Nota:</strong> Los cambios pueden estar sujetos a una tarifa de modificación y diferencias de precio.</p>
            <p><strong>Costo Adicional:</strong> <span>$5.00</span></p>
        </div>
        <footer class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('modify-modal')">Cancelar</button>
            <button class="btn-primary" id="confirm-modify">Continuar</button>
        </footer>
    </div>
</div>

<div id="cancel-modal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="cancel-title">
    <div class="modal-content">
        <header class="modal-header">
            <h3 id="cancel-title">Cancelar Reserva</h3>
            <button class="modal-close" onclick="closeModal('cancel-modal')" aria-label="Cerrar modal">×</button>
        </header>
        <div class="modal-body">
            <p>¿Está seguro que desea cancelar esta reserva? Se aplicará nuestra política de cancelación.</p>
            <div class="cancellation-policy">
                <h4>Política de Cancelación:</h4>
                <ul>
                    <li>Cancelación con más de 48h: Reembolso del 90%</li>
                    <!-- Se ha modificado la política de cancelación para reflejar el nuevo límite de 1h -->
                    <li>Cancelación entre 1-48h: Reembolso del 50%</li>
                    <li>Cancelación con menos de 1h: Sin reembolso</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="cancel-reason">Motivo de cancelación (opcional):</label>
                <textarea id="cancel-reason" name="cancel_reason" rows="3" placeholder="Ingrese el motivo de la cancelación..."></textarea>
            </div>
        </div>
        <footer class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('cancel-modal')">No Cancelar</button>
            <button class="btn-danger" id="confirm-cancel">Sí, Cancelar Reserva</button>
        </footer>
    </div>
</div>

<link rel="stylesheet" href="assets/css/reservations.css">
<script src="assets/js/reservations.js"></script>

<?php require_once 'includes/footer.php'; ?>
