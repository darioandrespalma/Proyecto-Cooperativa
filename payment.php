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

/* Mejoras de foco para accesibilidad */
button:focus,
input:focus,
a:focus {
    outline: 3px solid #005fcc;
    outline-offset: 2px;
}

/* Indicadores de estado para lectores de pantalla */
.form-group.error input {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.form-group.success input {
    border-color: #28a745;
    background-color: #f8fff8;
}

/* Estilos para mensajes de error */
.error-text {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 5px;
    display: none;
}

.error-text.show {
    display: block;
}

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
    <!-- Anuncio para lectores de pantalla -->
    <div role="status" aria-live="polite" class="sr-only" id="payment-status">
        Página de proceso de pago cargada
    </div>
    
    <main role="main">
        <h1 id="payment-title">Proceso de Pago</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" role="alert" aria-live="assertive">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Resumen de la Reserva -->
        <section class="reservation-summary" aria-labelledby="reservation-summary-title">
            <h2 id="reservation-summary-title">Resumen de tu Reserva</h2>
            
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
                    <div class="seats-display" role="list" aria-label="Lista de asientos seleccionados">
                        <?php 
                        $seat_map = ['ventana_izq'=>'A', 'pasillo_izq'=>'B', 'pasillo_der'=>'C', 'ventana_der'=>'D'];
                        foreach($asientos as $asiento): 
                        ?>
                            <span class="seat-badge" role="listitem" aria-label="Asiento fila <?= $asiento['fila'] ?> posición <?= $seat_map[$asiento['posicion']] ?? '?' ?>">
                                <?= $asiento['fila'] . ($seat_map[$asiento['posicion']] ?? '?') ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Resumen de Costos -->
                <div class="payment-total" role="table" aria-label="Desglose de costos">
                    <div class="total-row" role="row">
                        <span role="cell">Precio por asiento:</span>
                        <span role="cell">$<?= number_format($reserva['precio'], 2) ?></span>
                    </div>
                    <div class="total-row" role="row">
                        <span role="cell">Cantidad de asientos:</span>
                        <span role="cell"><?= $reserva['num_asientos'] ?></span>
                    </div>
                    <div class="total-row" role="row">
                        <span role="cell">Subtotal:</span>
                        <span role="cell">$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="total-row" role="row">
                        <span role="cell">IVA (12%):</span>
                        <span role="cell">$<?= number_format($iva, 2) ?></span>
                    </div>
                    <div class="total-row total-final" role="row">
                        <span role="cell">TOTAL A PAGAR:</span>
                        <span role="cell" aria-label="Total a pagar: <?= number_format($total, 2) ?> dólares">$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
                
            <?php else: ?>
                <p>No se encontró información de la reserva.</p>
            <?php endif; ?>
        </section>

        <!-- Métodos de Pago -->
        <section aria-labelledby="payment-methods-title">
            <h2 id="payment-methods-title">Selecciona tu Método de Pago</h2>
            <div class="payment-methods" role="radiogroup" aria-labelledby="payment-methods-title">
                <button id="btn-transferencia" class="payment-method active" 
                        role="radio" aria-checked="true" 
                        aria-describedby="transferencia-desc">
                    <strong><span aria-hidden="true">💳</span> Transferencia Bancaria</strong><br>
                    <small id="transferencia-desc">Sube tu comprobante de pago</small>
                </button>
                <button id="btn-tarjeta" class="payment-method" 
                        role="radio" aria-checked="false"
                        aria-describedby="tarjeta-desc">
                    <strong><span aria-hidden="true">💳</span> Tarjeta de Crédito/Débito</strong><br>
                    <small id="tarjeta-desc">Pago inmediato y seguro</small>
                </button>
            </div>
        </section>

        <!-- Formulario de Pago -->
        <section aria-labelledby="payment-form-title">
            <h2 id="payment-form-title" class="sr-only">Formulario de Pago</h2>
            <form id="payment-form" action="process/payment.php" method="post" enctype="multipart/form-data" 
                  novalidate aria-describedby="form-instructions">
                <div id="form-instructions" class="sr-only">
                    Completa la información del método de pago seleccionado. Los campos marcados con asterisco son obligatorios.
                </div>
                <input type="hidden" name="metodo" id="payment-method" value="transferencia">
                
                <!-- Formulario de Transferencia -->
                <fieldset id="transferencia-form" class="payment-form" aria-labelledby="transferencia-title">
                    <legend id="transferencia-title">Información de Transferencia</legend>
                    
                    <div class="bank-info" role="region" aria-labelledby="bank-info-title">
                        <h3 id="bank-info-title">Datos Bancarios - Cooperativa de Transporte</h3>
                        <p><strong>Banco:</strong> Banco del Pacífico</p>
                        <p><strong>Cuenta Corriente:</strong> 0123456789</p>
                        <p><strong>RUC:</strong> 1234567890001</p>
                        <p><strong>Beneficiario:</strong> Cooperativa de Transporte Intercantonal</p>
                        <p><strong>Monto a transferir:</strong> $<?= number_format($total, 2) ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="referencia">Número de Referencia de la Transferencia*</label>
                        <input type="text" id="referencia" name="referencia" required 
                               placeholder="Al menos 6 dígitos"
                               aria-invalid="false">
                        <div id="referencia-help" class="sr-only">Ingresa el número de referencia proporcionado por tu banco, debe tener al menos 6 dígitos</div>
                        <div id="referencia-error" class="error-text" role="alert" aria-live="polite"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comprobante">Comprobante de Transferencia*</label>
                        <div class="file-upload-area" onclick="document.getElementById('comprobante').click()" 
                             role="button" tabindex="0" 
                             aria-describedby="comprobante-help"
                             onkeydown="if(event.key==='Enter'||event.key===' ') document.getElementById('comprobante').click()">
                            <p><span aria-hidden="true">📎</span> Haz clic aquí para subir tu comprobante</p>
                            <p><small>Formatos permitidos: JPG, PNG, PDF (máx. 5MB)</small></p>
                        </div>
                        <input type="file" id="comprobante" name="comprobante" 
                               accept="image/*,.pdf" required style="display: none;"
                               aria-describedby="comprobante-help comprobante-error"
                               aria-invalid="false">
                        <div id="comprobante-help" class="sr-only">Sube una imagen o PDF del comprobante de tu transferencia bancaria</div>
                        <div id="comprobante-error" class="error-text" role="alert" aria-live="polite"></div>
                        <div id="file-preview" style="margin-top: 10px;" aria-live="polite"></div>
                    </div>
                </fieldset>
            
                <!-- Formulario de Tarjeta -->
                <fieldset id="tarjeta-form" class="payment-form" style="display:none;" aria-labelledby="tarjeta-title">
                    <legend id="tarjeta-title">Información de Tarjeta</legend>
                    
                    <div class="form-group">
                        <label for="numero_tarjeta">Número de Tarjeta*</label>
                        <input type="text" id="numero_tarjeta" name="numero_tarjeta" 
                               placeholder="1234 5678 9012 3456" maxlength="19"
                               aria-describedby="tarjeta-help tarjeta-error"
                               aria-invalid="false"
                               autocomplete="cc-number">
                        <div id="tarjeta-help" class="sr-only">Ingresa los 16 dígitos de tu tarjeta de crédito o débito</div>
                        <div id="tarjeta-error" class="error-text" role="alert" aria-live="polite"></div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_vencimiento">Fecha de Vencimiento*</label>
                            <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" 
                                   placeholder="MM/AA" maxlength="5"
                                   aria-describedby="fecha-help fecha-error"
                                   aria-invalid="false"
                                   autocomplete="cc-exp">
                            <div id="fecha-help" class="sr-only">Fecha de vencimiento en formato MM/AA</div>
                            <div id="fecha-error" class="error-text" role="alert" aria-live="polite"></div>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV*</label>
                            <input type="text" id="cvv" name="cvv" 
                                   placeholder="123" maxlength="3"
                                   aria-describedby="cvv-help cvv-error"
                                   aria-invalid="false"
                                   autocomplete="cc-csc">
                            <div id="cvv-help" class="sr-only">Código de 3 dígitos en la parte posterior de tu tarjeta</div>
                            <div id="cvv-error" class="error-text" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 10px; border-radius: 4px; margin-top: 15px;" role="note">
                        <small><span aria-hidden="true">🔒</span> Este es un sistema de demostración. Los datos de la tarjeta se procesan de forma simulada.</small>
                    </div>
                </fieldset>

                <div class="loading-spinner" id="loading-spinner" aria-hidden="true">
                    <div class="spinner" aria-hidden="true"></div>
                    <p>Procesando tu pago...</p>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn"
                        aria-describedby="submit-help">
                    Realizar Pago por $<?= number_format($total, 2) ?>
                </button>
                <div id="submit-help" class="sr-only">Al hacer clic procesarás el pago y confirmarás tu reserva</div>
            </form>
        </section>
    </main>
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
        setPaymentMethod('transferencia', this, btnTarjeta, formTransferencia, formTarjeta);
    });

    btnTarjeta.addEventListener('click', function() {
        setPaymentMethod('tarjeta', this, btnTransferencia, formTarjeta, formTransferencia);
    });
    
    // Función para cambiar método de pago con accesibilidad
    function setPaymentMethod(method, activeBtn, inactiveBtn, showForm, hideForm) {
        // Actualizar botones
        activeBtn.classList.add('active');
        activeBtn.setAttribute('aria-checked', 'true');
        inactiveBtn.classList.remove('active');
        inactiveBtn.setAttribute('aria-checked', 'false');
        
        // Mostrar/ocultar formularios
        showForm.style.display = 'block';
        hideForm.style.display = 'none';
        hideForm.setAttribute('aria-hidden', 'true');
        showForm.removeAttribute('aria-hidden');
        
        // Actualizar valor del método
        paymentMethod.value = method;
        
        // Enfocar primer campo del formulario activo
        const firstInput = showForm.querySelector('input:not([type="hidden"])');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
        
        // Anunciar cambio para lectores de pantalla
        const statusEl = document.getElementById('payment-status');
        statusEl.textContent = `Método de pago cambiado a ${method === 'transferencia' ? 'transferencia bancaria' : 'tarjeta de crédito'}`;
        
        // Actualizar campos requeridos
        updateRequiredFields(method);
    }
    
    function updateRequiredFields(method) {
        if (method === 'transferencia') {
            document.getElementById('comprobante').required = true;
            document.getElementById('referencia').required = true;
            document.getElementById('numero_tarjeta').required = false;
            document.getElementById('fecha_vencimiento').required = false;
            document.getElementById('cvv').required = false;
        } else {
            document.getElementById('comprobante').required = false;
            document.getElementById('referencia').required = false;
            document.getElementById('numero_tarjeta').required = true;
            document.getElementById('fecha_vencimiento').required = true;
            document.getElementById('cvv').required = true;
        }
    }
    
    // Manejo de archivo con accesibilidad
    const comprobanteInput = document.getElementById('comprobante');
    const filePreview = document.getElementById('file-preview');
    
    comprobanteInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            filePreview.innerHTML = `
                <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px;" role="status">
                    <strong>Archivo seleccionado:</strong> ${file.name}<br>
                    <small>Tamaño: ${fileSize} MB</small>
                </div>
            `;
            
            // Validar tamaño inmediatamente
            if (file.size > 5 * 1024 * 1024) {
                showFieldError('comprobante', 'El archivo es demasiado grande. Máximo 5MB');
            } else {
                clearFieldError('comprobante');
            }
        }
    });
    
    // Funciones para manejo de errores
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        const formGroup = field.closest('.form-group');
        
        field.setAttribute('aria-invalid', 'true');
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        const formGroup = field.closest('.form-group');
        
        field.setAttribute('aria-invalid', 'false');
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        
        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.classList.remove('show');
        }
    }
    
    // Formateo de número de tarjeta con validación
    const numeroTarjeta = document.getElementById('numero_tarjeta');
    if (numeroTarjeta) {
        numeroTarjeta.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = value;
            
            // Validación en tiempo real
            const cleanValue = value.replace(/\s/g, '');
            if (cleanValue.length > 0 && cleanValue.length < 16) {
                showFieldError('numero_tarjeta', 'El número de tarjeta debe tener 16 dígitos');
            } else if (cleanValue.length === 16) {
                clearFieldError('numero_tarjeta');
            }
        });
    }
    
    // Formateo de fecha de vencimiento con validación
    const fechaVencimiento = document.getElementById('fecha_vencimiento');
    if (fechaVencimiento) {
        fechaVencimiento.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
            
            // Validación en tiempo real
            if (value.length === 5) {
                const [mes, año] = value.split('/');
                const fechaActual = new Date();
                const añoCompleto = 2000 + parseInt(año);
                const fechaTarjeta = new Date(añoCompleto, parseInt(mes) - 1);
                
                if (parseInt(mes) < 1 || parseInt(mes) > 12) {
                    showFieldError('fecha_vencimiento', 'Mes inválido (01-12)');
                } else if (fechaTarjeta < fechaActual) {
                    showFieldError('fecha_vencimiento', 'La tarjeta está vencida');
                } else {
                    clearFieldError('fecha_vencimiento');
                }
            }
        });
    }
    
    
    
    // Limita a solo números y a un máximo de 6 dígitos
function limitarNumeros(inputId, maxDigitos) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('input', function () {
        // Quitar todo lo que no sea número
        this.value = this.value.replace(/\D/g, '');

        // Limitar a máximo X dígitos
        if (this.value.length > maxDigitos) {
            this.value = this.value.slice(0, maxDigitos);
        }

        // Limpia el mensaje si se cumple el mínimo
        if (this.value.length >= 6) {
            mostrarError('', inputId); // limpiar
        }
    });
}

// Mostrar mensaje de error en el div asociado
function mostrarError(mensaje, inputId) {
    const errorDiv = document.getElementById(`${inputId}-error`);
    if (errorDiv) {
        errorDiv.textContent = mensaje;
    }

    const input = document.getElementById(inputId);
    if (mensaje) {
        input.setAttribute('aria-invalid', 'true');
        input.style.border = '2px solid red';
    } else {
        input.removeAttribute('aria-invalid');
        input.style.border = '';
    }
}
// Limitar a 6 números como máximo
    limitarNumeros('referencia', 6);

    // Validar al salir del campo
    document.getElementById('referencia').addEventListener('blur', function () {
        if (this.value.length < 6) {
            mostrarError('El número de referencia debe tener al menos 6 dígitos', 'referencia');
        } else {
            mostrarError('', 'referencia'); // limpiar
        }
    });

    
    // Manejo del envío del formulario con accesibilidad mejorada
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpiar errores previos
        clearAllErrors();
        
        let hasErrors = false;
        
        // Validaciones según método de pago
        if (paymentMethod.value === 'transferencia') {
            const referencia = document.getElementById('referencia').value;
            const comprobante = document.getElementById('comprobante').files[0];
            
            if (!referencia || referencia.length < 6) {
                showFieldError('referencia', 'El número de referencia debe tener al menos 6 dígitos');
                hasErrors = true;
            }
            
            if (!comprobante) {
                showFieldError('comprobante', 'Debes subir el comprobante de transferencia');
                hasErrors = true;
            } else if (comprobante.size > 5 * 1024 * 1024) {
                showFieldError('comprobante', 'El archivo es demasiado grande. Máximo 5MB');
                hasErrors = true;
            }
            
        } else if (paymentMethod.value === 'tarjeta') {
            const numeroTarjeta = document.getElementById('numero_tarjeta').value.replace(/\s/g, '');
            const fechaVenc = document.getElementById('fecha_vencimiento').value;
            const cvv = document.getElementById('cvv').value;
            
            if (numeroTarjeta.length !== 16) {
                showFieldError('numero_tarjeta', 'El número de tarjeta debe tener 16 dígitos');
                hasErrors = true;
            }
            
            if (!/^\d{2}\/\d{2}$/.test(fechaVenc)) {
                showFieldError('fecha_vencimiento', 'Formato de fecha inválido (MM/AA)');
                hasErrors = true;
            } else {
                // Validar que la fecha no esté vencida
                const [mes, año] = fechaVenc.split('/');
                const fechaActual = new Date();
                const añoCompleto = 2000 + parseInt(año);
                const fechaTarjeta = new Date(añoCompleto, parseInt(mes) - 1);
                
                if (fechaTarjeta < fechaActual) {
                    showFieldError('fecha_vencimiento', 'La tarjeta está vencida');
                    hasErrors = true;
                }
            }
            
            if (cvv.length !== 3) {
                showFieldError('cvv', 'El CVV debe tener 3 dígitos');
                hasErrors = true;
            }
        }
        
        if (hasErrors) {
            // Enfocar el primer campo con error
            const firstError = document.querySelector('.form-group.error input');
            if (firstError) {
                firstError.focus();
            }
            
            // Anunciar error para lectores de pantalla
            const statusEl = document.getElementById('payment-status');
            statusEl.textContent = 'Por favor corrige los errores en el formulario';
            return;
        }
        
        // Mostrar loading y deshabilitar botón
        submitBtn.disabled = true;
        submitBtn.textContent = 'Procesando...';
        submitBtn.setAttribute('aria-busy', 'true');
        loadingSpinner.style.display = 'block';
        loadingSpinner.setAttribute('aria-hidden', 'false');
        
        // Anunciar procesamiento
        const statusEl = document.getElementById('payment-status');
        statusEl.textContent = 'Procesando pago, por favor espera...';
        
        // Enviar formulario
        this.submit();
    });
    
    function clearAllErrors() {
        const errorElements = document.querySelectorAll('.error-text');
        errorElements.forEach(el => {
            el.textContent = '';
            el.classList.remove('show');
        });
        
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach(group => {
            group.classList.remove('error');
        });
        
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.setAttribute('aria-invalid', 'false');
        });
    }
    
    // Prevenir doble envío
    let submitted = false;
    paymentForm.addEventListener('submit', function() {
        if (submitted) return false;
        submitted = true;
    });
    
    // Navegación por teclado para área de subida de archivos
    const fileUploadArea = document.querySelector('.file-upload-area');
    if (fileUploadArea) {
        fileUploadArea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                document.getElementById('comprobante').click();
            }
        });
    }
    
    // Manejadores de eventos de radiogroup para métodos de pago
    document.addEventListener('keydown', function(e) {
        const focusedElement = document.activeElement;
        if (focusedElement.classList.contains('payment-method')) {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (focusedElement === btnTarjeta) {
                    btnTransferencia.click();
                    btnTransferencia.focus();
                }
            } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                if (focusedElement === btnTransferencia) {
                    btnTarjeta.click();
                    btnTarjeta.focus();
                }
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>