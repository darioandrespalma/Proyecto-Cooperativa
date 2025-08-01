<?php 
require_once 'includes/header.php';
require_once 'includes/functions.php';

$schedule_id = $_GET['schedule_id'] ?? null;
$bus_id = $_GET['bus_id'] ?? null;

if (!$schedule_id || !$bus_id) {
    header('Location: routes.php');
    exit;
}

// En un sistema real, verificar disponibilidad de asientos
?>
    <section aria-labelledby="seats-title">
        <h1 id="seats-title">Seleccione sus Asientos</h1>
        
        <div class="bus-layout">
            <?php for ($fila = 1; $fila <= 10; $fila++): ?>
                <div class="bus-row">
                    <div class="seat" data-fila="<?= $fila ?>" data-posicion="ventana_izq" tabindex="0" role="button" aria-label="Asiento fila <?= $fila ?> ventana izquierda">
                        <?= $fila ?>A
                    </div>
                    <div class="seat" data-fila="<?= $fila ?>" data-posicion="pasillo_izq" tabindex="0" role="button" aria-label="Asiento fila <?= $fila ?> pasillo izquierdo">
                        <?= $fila ?>B
                    </div>
                    <div class="seat" data-fila="<?= $fila ?>" data-posicion="pasillo_der" tabindex="0" role="button" aria-label="Asiento fila <?= $fila ?> pasillo derecho">
                        <?= $fila ?>C
                    </div>
                    <div class="seat" data-fila="<?= $fila ?>" data-posicion="ventana_der" tabindex="0" role="button" aria-label="Asiento fila <?= $fila ?> ventana derecha">
                        <?= $fila ?>D
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <form id="reservation-form" action="process/reservation.php" method="post">
            <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
            <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
            <input type="hidden" name="asientos[]" id="selected-seats">
            
            <div id="selected-seats-display" aria-live="polite"></div>
            
            <button type="submit">Confirmar Reserva</button>
        </form>
    </section>
    
    <script src="/proyecto_cooperativa/assets/js/bus-selection.js"></script>
<?php require_once 'includes/footer.php'; ?>