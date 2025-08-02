<?php 
session_start();

require_once 'includes/header.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['passenger_id'])) {
    header("Location: login.php");
    exit;
}

$schedule_id = $_GET['schedule_id'] ?? null;
$bus_id = $_GET['bus_id'] ?? null;
$ruta_id = $_SESSION['ruta_id'] ?? null;

if (!$schedule_id || !$bus_id || !$ruta_id) {
    header('Location: routes.php');
    exit;
}

require_once 'includes/db_connect.php';

// Obtener asientos ocupados
$statement = $pdo->prepare("
    SELECT rs.fila, rs.posicion 
    FROM reservation_seats rs
    JOIN reservations r ON rs.reservation_id = r.id
    WHERE rs.bus_id = ? AND r.schedule_id = ?
");
$statement->execute([$bus_id, $schedule_id]);
$occupiedSeats = $statement->fetchAll(PDO::FETCH_ASSOC);

// Formatear como "fila_posicion"
$asientos_ocupados = array_map(fn($s) => "{$s['fila']}_{$s['posicion']}", $occupiedSeats);
?>

<section>
    <h2>Seleccione sus Asientos</h2>
    <div class="bus-layout">
        <?php for ($fila = 1; $fila <= 10; $fila++): ?>
            <div class="bus-row">
                <?php
                $posiciones = ['ventana_izq', 'pasillo_izq', 'pasillo_der', 'ventana_der'];
                $letras = ['A', 'B', 'C', 'D'];
                for ($i = 0; $i < 4; $i++):
                    $pos = $posiciones[$i];
                    $letra = $letras[$i];
                    $seatId = "{$fila}_{$pos}";
                    $ocupado = in_array($seatId, $asientos_ocupados);
                ?>
                <div class="seat <?= $ocupado ? 'occupied' : '' ?>" 
                     data-seat="<?= $seatId ?>" 
                     tabindex="<?= $ocupado ? '-1' : '0' ?>" 
                     <?= $ocupado ? 'aria-disabled="true"' : '' ?>>
                     <?= $fila . $letra ?>
                </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>

    <form id="reservation-form" action="process/reservation.php" method="post">
        <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
        <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
        <input type="hidden" name="ruta_id" value="<?= $ruta_id ?>">
        <div id="asientos-container"></div>
        <button type="submit">Confirmar Reserva</button>
    </form>
</section>

<script>
const selectedSeats = new Set();
document.querySelectorAll('.seat:not(.occupied)').forEach(seat => {
    seat.addEventListener('click', () => {
        const seatId = seat.dataset.seat;
        if (selectedSeats.has(seatId)) {
            selectedSeats.delete(seatId);
            seat.classList.remove('selected');
        } else {
            selectedSeats.add(seatId);
            seat.classList.add('selected');
        }
        updateHiddenInputs();
    });
});

function updateHiddenInputs() {
    const container = document.getElementById('asientos-container');
    container.innerHTML = '';
    selectedSeats.forEach(seat => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'asientos[]';
        input.value = seat;
        container.appendChild(input);
    });
}
</script>

<style>
.seat {
    display: inline-block;
    padding: 10px;
    margin: 5px;
    border: 1px solid #ccc;
    cursor: pointer;
}
.seat.occupied {
    background-color: #ccc;
    cursor: not-allowed;
}
.seat.selected {
    background-color: #4CAF50;
    color: white;
}
</style>

<?php require_once 'includes/footer.php'; ?>
