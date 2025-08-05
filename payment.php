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

// Obtener detalles completos de la reserva
$stmt = $pdo->prepare("
    SELECT r.id AS reserva_id, r.fecha_reserva, r.estado,
           p.nombre, p.apellido, p.cedula, p.email,
           ro.nombre AS ruta, ro.precio,
           s.hora, s.dias_semana, s.tipo_servicio,
           b.placa, b.modelo, b.tipo AS tipo_bus,
           COUNT(rs.id) as num_asientos
    FROM reservations r
    JOIN passengers p ON r.passenger_id = p.id
    JOIN schedules s ON s.id = r.schedule_id
    JOIN routes ro ON s.ruta_id = ro.id
    JOIN buses b ON b.id = r.bus_id
    JOIN reservation_seats rs ON rs.reservation_id = r.id
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

// Calcular totales
$subtotal = 0;
$iva = 0;
$total = 0;

if ($reserva) {
    $subtotal = $reserva['precio'] * $reserva['num_asientos'];
    $iva = $subtotal * 0.12; // 12% IVA
    $total = $subtotal + $iva;
}
?>

<style>
.payment-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.reservation-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.summary-item:last-child {
    border-bottom: none;
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
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
}

.payment-total {
    background: #e9ecef;
    padding: 15px;
    border-radius: 6px;
    margin-top: 15px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.total-final {
    font-size: 1.2em;
    font-weight: bold;
    border-top: 2px solid #007bff;
    padding-top: 10px;
    margin-top: 10px;
}

.payment-methods {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
}

.payment-method {
    flex: 1;
    padding: 15px;
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}

.payment-method.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.payment-method:hover {
    border-color: #007bff;
}

.payment-form {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-area:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.file-upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-submit:hover {
    background: #218838;
}

.btn-submit:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 20px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bank-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.bank-info h4 {
    margin: 0 0 10px 0;
    color: #1565c0;
}

.bank-info p {
    margin: 5px 0;
    font-size: 14px;
}

@media (max-width: 768px) {
    .payment-container {
        padding: 10px;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-methods {
        flex-direction: column;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="payment-container">
    <section aria-labelledby="payment-title">
        <h1 id="payment-title">Proceso de Pago</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Resumen de la Reserva -->
        <div class="reservation-summary">
            <h2>Resumen de tu Reserva</h2>
            
            <?php if ($reserva): ?>
                <div class="summary-grid">
                    <div>
                        <div class="summary-item">
                            <strong>Pasajero:</strong>
                            <span><?= htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']) ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Cédula:</strong>
                            <span><?= htmlspecialchars($reserva['cedula']) ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Email:</strong>
                            <span><?= htmlspecialchars($reserva['email']) ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="summary-item">
                            <strong>Ruta:</strong>
                            <span><?= htmlspecialchars($reserva['ruta']) ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Horario:</strong>
                            <span><?= htmlspecialchars($reserva['hora']) ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Días:</strong>
                            <span><?= htmlspecialchars($reserva['dias_semana']) ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Servicio:</strong>
                            <span><?= htmlspecialchars($reserva['tipo_servicio']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="summary-item">
                    <strong>Bus:</strong>
                    <span><?= htmlspecialchars($reserva['placa'] . ' - ' . $reserva['modelo'] . ' (' . $reserva['tipo_bus'] . ')') ?></span>
                </div>
                
                <div class="summary-item">
                    <strong>Asientos Seleccionados:</strong>
                    <div class="seats-display">
                        <?php 
                        $seat_map = ['ventana_izq'=>'A', 'pasillo_izq'=>'B', 'pasillo_der'=>'C', 'ventana_der'=>'D'];
                        foreach($asientos as $asiento): 
                        ?>
                            <span class="seat-badge">
                                <?= $asiento['fila'] . ($seat_map[$asiento['posicion']] ?? '?') ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Resumen de Costos -->
                <div class="payment-total">
                    <div class="total-row">
                        <span>Precio por asiento:</span>
                        <span>$<?= number_format($reserva['precio'], 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Cantidad de asientos:</span>
                        <span><?= $reserva['num_asientos'] ?></span>
                    </div>
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>IVA (12%):</span>
                        <span>$<?= number_format($iva, 2) ?></span>
                    </div>
                    <div class="total-row total-final">
                        <span>TOTAL A PAGAR:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
                
            <?php else: ?>
                <p>No se encontró información de la reserva.</p>
            <?php endif; ?>
        </div>

        <!-- Métodos de Pago -->
        <h2>Selecciona tu Método de Pago</h2>
        <div class="payment-methods">
            <button id="btn-transferencia" class="payment-method active" aria-pressed="true">
                <strong>💳 Transferencia Bancaria</strong><br>
                <small>Sube tu comprobante de pago</small>
            </button>
            <button id="btn-tarjeta" class="payment-method" aria-pressed="false">
                <strong>💳 Tarjeta de Crédito/Débito</strong><br>
                <small>Pago inmediato y seguro</small>
            </button>
        </div>

        <!-- Formulario de Pago -->
        <form id="payment-form" action="process/payment.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="metodo" id="payment-method" value="transferencia">
            
            <!-- Formulario de Transferencia -->
            <div id="transferencia-form" class="payment-form">
                <h3>Información de Transferencia</h3>
                
                <div class="bank-info">
                    <h4>Datos Bancarios - Cooperativa de Transporte</h4>
                    <p><strong>Banco:</strong> Banco del Pacífico</p>
                    <p><strong>Cuenta Corriente:</strong> 0123456789</p>
                    <p><strong>RUC:</strong> 1234567890001</p>
                    <p><strong>Beneficiario:</strong> Cooperativa de Transporte Intercantonal</p>
                    <p><strong>Monto a transferir:</strong> $<?= number_format($total, 2) ?></p>
                </div>
                
                <div class="form-group">
                    <label for="referencia">Número de Referencia de la Transferencia*</label>
                    <input type="text" id="referencia" name="referencia" required 
                           placeholder="Ingresa el número de referencia de tu transferencia">
                </div>
                
                <div class="form-group">
                    <label for="comprobante">Comprobante de Transferencia*</label>
                    <div class="file-upload-area" onclick="document.getElementById('comprobante').click()">
                        <p>📎 Haz clic aquí para subir tu comprobante</p>
                        <p><small>Formatos permitidos: JPG, PNG, PDF (máx. 5MB)</small></p>
                    </div>
                    <input type="file" id="comprobante" name="comprobante" 
                           accept="image/*,.pdf" required style="display: none;">
                    <div id="file-preview" style="margin-top: 10px;"></div>
                </div>
            </div>
            
            <!-- Formulario de Tarjeta -->
            <div id="tarjeta-form" class="payment-form" style="display:none;">
                <h3>Información de Tarjeta</h3>
                
                <div class="form-group">
                    <label for="numero_tarjeta">Número de Tarjeta*</label>
                    <input type="text" id="numero_tarjeta" name="numero_tarjeta" 
                           placeholder="1234 5678 9012 3456" maxlength="19">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha de Vencimiento*</label>
                        <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" 
                               placeholder="MM/AA" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV*</label>
                        <input type="text" id="cvv" name="cvv" 
                               placeholder="123" maxlength="3">
                    </div>
                </div>
                
                <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 10px; border-radius: 4px; margin-top: 15px;">
                    <small>🔒 Este es un sistema de demostración. Los datos de la tarjeta se procesan de forma simulada.</small>
                </div>
            </div>

            <div class="loading-spinner" id="loading-spinner">
                <div class="spinner"></div>
                <p>Procesando tu pago...</p>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                Realizar Pago por $<?= number_format($total, 2) ?>
            </button>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnTransferencia = document.getElementById('btn-transferencia');
    const btnTarjeta = document.getElementById('btn-tarjeta');
    const formTransferencia = document.getElementById('transferencia-form');
    const formTarjeta = document.getElementById('tarjeta-form');
    const paymentMethod = document.getElementById('payment-method');
    const paymentForm = document.getElementById('payment-form');
    const submitBtn = document.getElementById('submit-btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    // Cambio de método de pago
    btnTransferencia.addEventListener('click', function() {
        this.classList.add('active');
        this.setAttribute('aria-pressed', 'true');
        btnTarjeta.classList.remove('active');
        btnTarjeta.setAttribute('aria-pressed', 'false');
        formTransferencia.style.display = 'block';
        formTarjeta.style.display = 'none';
        paymentMethod.value = 'transferencia';
        
        // Actualizar campos requeridos
        document.getElementById('comprobante').required = true;
        document.getElementById('referencia').required = true;
        document.getElementById('numero_tarjeta').required = false;
        document.getElementById('fecha_vencimiento').required = false;
        document.getElementById('cvv').required = false;
    });

    btnTarjeta.addEventListener('click', function() {
        this.classList.add('active');
        this.setAttribute('aria-pressed', 'true');
        btnTransferencia.classList.remove('active');
        btnTransferencia.setAttribute('aria-pressed', 'false');
        formTransferencia.style.display = 'none';
        formTarjeta.style.display = 'block';
        paymentMethod.value = 'tarjeta';
        
        // Actualizar campos requeridos
        document.getElementById('comprobante').required = false;
        document.getElementById('referencia').required = false;
        document.getElementById('numero_tarjeta').required = true;
        document.getElementById('fecha_vencimiento').required = true;
        document.getElementById('cvv').required = true;
    });
    
    // Manejo de archivo
    const comprobanteInput = document.getElementById('comprobante');
    const filePreview = document.getElementById('file-preview');
    
    comprobanteInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            filePreview.innerHTML = `
                <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px;">
                    <strong>Archivo seleccionado:</strong> ${file.name}<br>
                    <small>Tamaño: ${fileSize} MB</small>
                </div>
            `;
        }
    });
    
    // Formateo de número de tarjeta
    const numeroTarjeta = document.getElementById('numero_tarjeta');
    if (numeroTarjeta) {
        numeroTarjeta.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = value;
        });
    }
    
    // Formateo de fecha de vencimiento
    const fechaVencimiento = document.getElementById('fecha_vencimiento');
    if (fechaVencimiento) {
        fechaVencimiento.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });
    }
    
    // Solo números en CVV
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    
    // Solo números en referencia
    const referenciaInput = document.getElementById('referencia');
    if (referenciaInput) {
        referenciaInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    
    // Manejo del envío del formulario
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validaciones adicionales
        if (paymentMethod.value === 'transferencia') {
            const referencia = document.getElementById('referencia').value;
            const comprobante = document.getElementById('comprobante').files[0];
            
            if (!referencia || referencia.length < 6) {
                alert('El número de referencia debe tener al menos 6 dígitos');
                return;
            }
            
            if (!comprobante) {
                alert('Debes subir el comprobante de transferencia');
                return;
            }
            
            // Validar tamaño del archivo
            if (comprobante.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB');
                return;
            }
            
        } else if (paymentMethod.value === 'tarjeta') {
            const numeroTarjeta = document.getElementById('numero_tarjeta').value.replace(/\s/g, '');
            const fechaVenc = document.getElementById('fecha_vencimiento').value;
            const cvv = document.getElementById('cvv').value;
            
            if (numeroTarjeta.length !== 16) {
                alert('El número de tarjeta debe tener 16 dígitos');
                return;
            }
            
            if (!/^\d{2}\/\d{2}$/.test(fechaVenc)) {
                alert('Formato de fecha inválido (MM/AA)');
                return;
            }
            
            // Validar que la fecha no esté vencida
            const [mes, año] = fechaVenc.split('/');
            const fechaActual = new Date();
            const añoCompleto = 2000 + parseInt(año);
            const fechaTarjeta = new Date(añoCompleto, parseInt(mes) - 1);
            
            if (fechaTarjeta < fechaActual) {
                alert('La tarjeta está vencida');
                return;
            }
            
            if (cvv.length !== 3) {
                alert('El CVV debe tener 3 dígitos');
                return;
            }
        }
        
        // Mostrar loading y deshabilitar botón
        submitBtn.disabled = true;
        submitBtn.textContent = 'Procesando...';
        loadingSpinner.style.display = 'block';
        
        // Enviar formulario
        this.submit();
    });
    
    // Prevenir doble envío
    let submitted = false;
    paymentForm.addEventListener('submit', function() {
        if (submitted) return false;
        submitted = true;
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>